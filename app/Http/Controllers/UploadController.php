<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Http\Request;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Log;




class UploadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $uploads = Auth::user()->uploads;
        return view('uploads.index', compact('uploads'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // 🔽 追加
        return view('uploads.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // 1. バリデーションのデバッグ
            Log::info('Starting file upload process');
            
            // $validated = $request->validate([
            //     'file' => 'required|mimes:mp3',
            // ]);
            // Log::info('File validation passed');

            //dd($validated);
    
            // 2. ファイル情報のデバッグ
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            Log::info('File details', [
                'name' => $fileName,
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
                'path' => $file->getPathname()
            ]);

            // 3. S3設定のデバッグ
            $s3Config = [
                'version' => 'latest',
                'region'  => env('AWS_DEFAULT_REGION'),
                'endpoint' => env('AWS_ENDPOINT'),
                'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
                'credentials' => [
                    'key'    => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ]
            ];
            Log::info('S3 configuration', [
                'region' => $s3Config['region'],
                'endpoint' => $s3Config['endpoint'],
                'bucket' => env('AWS_BUCKET')
            ]);

            // 4. S3クライアント作成
            $s3 = new S3Client($s3Config);
            Log::info('S3 client created successfully');

            // 5. S3アップロード
            $bucket = env('AWS_BUCKET');
            $key = $fileName;
            
            $uploadParams = [
                'Bucket' => $bucket,
                'Key'    => $key,
                'SourceFile' => $file->getPathname(),
            ];
            Log::info('Attempting S3 upload with params', $uploadParams);

            $result = $s3->putObject($uploadParams);
            Log::info('S3 upload successful', ['url' => $result['ObjectURL']]);

            // 6. データベース保存
            $upload = $request->user()->uploads()->create([
                'title' => $fileName,
                'mp3_url' => $result['ObjectURL'],
            ]);
            Log::info('Database record created', ['upload_id' => $upload->id]);

            return redirect()
                ->route('uploads.index')
                ->with('success', 'File uploaded successfully')
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
}































// namespace App\Http\Controllers;

// use App\Models\Upload;
// use Illuminate\Http\Request;

// use Aws\S3\S3Client;
// use Aws\Exception\AwsException;
// use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Auth; 
// use Illuminate\Support\Facades\Log;




// class UploadController extends Controller
// {
//     /**
//      * Display a listing of the resource.
//      */
//     public function index()
//     {
//        $uploads = Auth::user()->uploads;
//         return view('uploads.index', compact('uploads'));
//     }

//     /**
//      * Show the form for creating a new resource.
//      */
//     public function create()
//     {
//         // 🔽 追加
//         return view('uploads.create');
//     }

//     /**
//      * Store a newly created resource in storage.
//      */
//     public function store(Request $request)
//     {
//         $request->validate([
//             'file' => 'required|mimes:mp3',
            
//         ]);

//         //dd($request);

//         $file = $request->file('file');
//         $fileName = $file->getClientOriginalName();

//         //dd($file);//originalName: "02 September.mp3"

//         // S3にアップロード
//         $s3 = new S3Client([
//             'version' => 'latest',
//             'region'  => env('AWS_DEFAULT_REGION'),
	   
// 	    'endpoint' => env('AWS_ENDPOINT'), // エンドポイントを追加
// 	    'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),//エンドポイントを追加

// 	    'credentials' => [
//                 'key'    => env('AWS_ACCESS_KEY_ID'),
//                 'secret' => env('AWS_SECRET_ACCESS_KEY'),
//             ],
//         ]);

//         $bucket = env('AWS_BUCKET');
//         $key =  $fileName;

//         //dd($key);  //ファイル名取得できている//"02 September.mp3"

//         try {
//             $result = $s3->putObject([
//                 'Bucket' => $bucket,
//                 'Key'    => $key,
//                 'SourceFile' => $file->getPathname(),
                
//             ]);

//             // アップロードされたファイルのURLを取得
//             $s3Url = $result['ObjectURL'];

//             // データベースに保存
//             $request->user()->uploads()->create([
//                 'title' => $fileName, // ファイル名をタイトルとして保存
//                 'mp3_url' => $s3Url,
//             ]);


//             //dd($fileName); //ファイル名取得できている//"02 September.mp3"
            
//             //$title = $request->input('title');⇒入力フォームにtitleとかないのでnullなので不要
//             //dd($title);//null

//             return redirect()->route('uploads.index')->with('s3_url', $s3Url);
//             } catch (AwsException $e) {
//             // エラーメッセージをログに記録
//             Log::error('S3 Upload Error: ' . $e->getMessage());
//             // エラーメッセージをJSON形式で返す
//             return response()->json(['error' => $e->getMessage()], 500);
//             } catch (\Exception $e) {
//             // その他の例外をキャッチしてログに記録
//             Log::error('General Error: ' . $e->getMessage(), ['exception' => $e]);
//             return response()->json(['error' => $e->getMessage()], 500);
//         }
//     }
    

//     /**
//      * Display the specified resource.
//      */
//     public function show(Upload $upload)
//     {
//         return view('uploads.show', compact('upload'));
//     }

//     /**
//      * Show the form for editing the specified resource.
//      */
//     public function edit(Upload $upload)
//     {
//         //
//     }

//     /**
//      * Update the specified resource in storage.
//      */
//     public function update(Request $request, Upload $upload)
//     {
//         //
//     }

//     /**
//      * Remove the specified resource from storage.
//      */
//     public function destroy(Upload $upload)
//     {
//         $upload->delete();

//         return redirect()->route('uploads.index');
//     }
// }
