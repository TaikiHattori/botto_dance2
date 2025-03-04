<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Http\Request;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class UploadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // ログインユーザーのアップロードを取得（降順） 
        $uploads = Auth::user()->uploads()->orderBy('created_at', 'desc')->get();

        //ログインユーザーに紐づくupload_idの数を取得
        $getCountId = $uploads->count();

        return view('uploads.index', compact('uploads', 'getCountId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // 認可ポリシーを適用　※UP権限
        if (Gate::denies('upload')) {
            abort(403, 'This action is unauthorized.');
        }
        
        // 🔽 追加
        return view('uploads.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {        
        try {
            //  バリデーションのデバッグ
            Log::info('Starting file upload process');
            
            $validated = $request->validate([
                'files.*' => 'required|mimes:mp3',
            ]);
            Log::info('File validation passed');
    
            //--------------------------------
            //  複数UPループ処理
            //--------------------------------
            $files = $request->file('files');
            dd($files);

            foreach($files as $file){
            $fileName = $file->getClientOriginalName();
            
            Log::info('File details', [
                'name' => $fileName,
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
                'path' => $file->getPathname()
            ]);

            //曲の長さを取得
            $filePath = $file->getPathname();
            $duration = $this->getAudioDuration($filePath);
            Log::info('File duration', ['duration' => $duration]);

            //----------------------------
            //  S3アップロード
            //----------------------------

            //  S3設定のデバッグ
            $s3Config = [
                'version' => 'latest',
                'region'  => config('filesystems.disks.s3.region'),
                'endpoint' => config('sample.endpoint'),
                'use_path_style_endpoint' => config('sample.use_path_style_endpoint', false),
                'credentials' => [
                    'key'    => config('sample.key'),
                    'secret' => config('sample.secret'),
                ]
            ];

            Log::info('S3 configuration', [
                'region' => $s3Config['region'],
                'endpoint' => $s3Config['endpoint'],
                'bucket' => config('sample.bucket')
            ]);

            //  S3クライアント作成
            $s3 = new S3Client($s3Config);
            Log::info('S3 client created successfully');

            $bucket = config('sample.bucket');
            //ユーザー名を取得
            $userName = Auth::user()->name;
            //$keyをユーザー名を含む形に変更（mp3_urlカラムなので）
            $key = $userName . '/' . $fileName;

            $uploadParams = [
                'Bucket' => $bucket,
                'Key'    => $key,
                'SourceFile' => $file->getPathname(),
            ];
            Log::info('Attempting S3 upload with params', $uploadParams);

            try {
                $result = $s3->putObject($uploadParams);
                Log::info('S3 upload successful', ['url' => $result['ObjectURL']]);
            } catch (AwsException $e) {
                Log::error('AWS Error during S3 upload', [
                    'message' => $e->getMessage(),
                    'code' => $e->getAwsErrorCode(),
                    'type' => $e->getAwsErrorType(),
                    'request_id' => $e->getAwsRequestId()
                ]);
                continue; // 次のmp3ファイルに進む
            }

            //ジャンルログ
            $genreSelect = $request->genreSelect;//selectタグの取得（name属性を指定）
            $genreInput = $request->input('genreInput');//inputタグの取得（name属性を指定）
            $genre = $genreSelect ? $genreSelect : $genreInput;//三項演算子の構文
            Log::info('genre received from form', ['genre' => $genre]);

            //  データベース保存ログ
            Log::info('Saving to database', [
            'title' => $fileName,
            'mp3_url' => $result['ObjectURL'],
            'duration' => $duration, //曲の長さ
            'genre' => $genre,
            ]);

            //  データベース保存
            $upload = $request->user()->uploads()->create([
                'title' => $fileName,
                'mp3_url' => $result['ObjectURL'],
                'duration' => $duration,//曲の長さ
                'genre' => $genre,
            ]);
            Log::info('Database record created', ['upload_id' => $upload->id]);
        }
            
        return redirect()
            ->route('uploads.index')
            ->with('success', 'Files uploaded successfully')
            ->with('s3_url', $result['ObjectURL']);




        } catch (AwsException $e) {
            Log::error('AWS Error', [
                'message' => $e->getMessage(),
                'code' => $e->getAwsErrorCode(),
                'type' => $e->getAwsErrorType(),
                'request_id' => $e->getAwsRequestId()
            ]);
            return back()
                ->withErrors(['error' => 'AWS Error: ' . $e->getAwsErrorMessage()])
                ->withInput();

        } catch (\Exception $e) {
            Log::error('General Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return back()
                ->withErrors(['error' => 'Upload failed: ' . $e->getMessage()])
                ->withInput();
        }
    }
    
    private function getAudioDuration($filePath)
    {
        //getAudioDurationメソッドは、
        //内部的な処理に使用されるヘルパーメソッドであり、外部から直接呼び出される必要がないため
        //「private」function

        //  FFmpegコマンドで曲の長さを取得
        $command = "ffmpeg -i " . escapeshellarg($filePath) . " 2>&1 | grep 'Duration'";
        $output = shell_exec($command);

        Log::info('FFmpeg output', ['output' => $output]);

        //出力を解析して曲の長さを計算
        if (preg_match('/Duration: (\d+):(\d+):(\d+\.\d+)/', $output, $matches)) {
            $hours = (int)$matches[1];
            $minutes = (int)$matches[2];
            $seconds = (float)$matches[3];
            $totalSeconds = ($hours * 3600) + ($minutes * 60) + $seconds;
            
            //分と秒に変換
            $minutes = floor($totalSeconds / 60);
            $seconds = $totalSeconds % 60;

            //mm:ss形式で返す
            return sprintf('%02d:%02d', $minutes, $seconds);
        }

        return '00:00';
    }

    /**
     * Display the specified resource.
     */
    public function show(Upload $upload)
    {
        return view('uploads.show', compact('upload'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Upload $upload)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Upload $upload)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Upload $upload)
    {
        $upload->delete();

        return redirect()->route('uploads.index');
    }

    public function bulkDelete(Request $request)
    {
        Log::info('Request: ',  $request->all());

        $ids = $request->input('uploads');
        Log::info('Ids to delete: ' , ['ids' => $ids]);

        if ($ids) {
            //S3クライアントの設定
            $s3Config = [
                'version' => 'latest',//SDKの最新バージョン使うために必要
                'region'  => config('sample.region'),
                'endpoint' => config('sample.endpoint'),
                'use_path_style_endpoint' => config('sample.use_path_style_endpoint', false),
                'credentials' => [
                'key'    => config('sample.key'),
                'secret' => config('sample.secret'),
                ]
            ];

            //S3クライアントの作成
            $s3 = new S3Client($s3Config);
            $bucket = config('sample.bucket');

            //DBから削除するレコードを取得
            $uploads = Upload::whereIn('id', $ids)->get();

            foreach ($uploads as $upload) {
                try {
                    // $file = $request->file('file');
                    // $fileName = $file->getClientOriginalName();

                    //S3のオブジェクト削除
                    $key = parse_url($upload->mp3_url, PHP_URL_PATH);                    
                    $key = ltrim($key, '/');//先頭のスラッシュを削除
                    $key = urldecode($key);//曲UP時URLデコード状態なので、削除時もURLデコードじゃないと削除できない
                    // dd($key);

                    //AWS SDK for PHPでdeleteObjectメソッドを呼び出す
                    $s3->deleteObject([
                        'Bucket' => $bucket,
                        'Key' => $key,
                    ]);
                    Log::info('S3 object deleted', ['key' => $key]);

                    // データベースからレコードを削除
                    $upload->delete();

                    Log::info('Database record deleted successfully', ['upload_id' => $upload->id]);
                } catch (AwsException $e) {
                    Log::error('AWS Error', [
                        'message' => $e->getMessage(),
                        'code' => $e->getAwsErrorCode(),
                        'type' => $e->getAwsErrorType(),
                        'request_id' => $e->getAwsRequestId()
                    ]);
                } catch (\Exception $e) {
                    Log::error('General Error', [
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]);
                }
            }
        }
        
        return redirect()->route('uploads.index');
    }
}


