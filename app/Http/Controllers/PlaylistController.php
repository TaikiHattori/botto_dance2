<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use App\Models\Extraction;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth; // Authファサードをインポート（checkメソッドのために）

use Aws\S3\S3Client;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PlaylistController extends Controller
{
    public function check($id)
    {        
        $upload = Upload::findOrFail($id);

        if ($upload->user_id === Auth::id()) {
            return response()->json(['allowed' => true]);
        }

        return response()->json(['allowed' => false]);
    }
    
    public function play($id)
    {
        // Extractionデータを取得
        $extraction = Extraction::findOrFail($id);
        
        $upload = Upload::findOrFail($extraction->upload_id);

        // S3クライアントの設定
        $s3 = new S3Client([
            'version' => 'latest',
            'region' => config('filesystems.disks.s3.region'),
            'credentials' => [
                'key' => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
        ]);



        // オブジェクトの取得
        $bucket = config('filesystems.disks.s3.bucket');
        $key = ltrim(urldecode(parse_url($upload->mp3_url, PHP_URL_PATH)), '/');
        
        // ストリーミングレスポンスの作成
        $response = new StreamedResponse(function() use ($s3, $bucket, $key) {
            $result = $s3->getObject([
                'Bucket' => $bucket,
                'Key' => $key,
            ]);
            
            echo $result['Body'];
        });
    
        $response->headers->set('Content-Type', 'audio/mpeg');
        $response->headers->set('Content-Disposition', 'inline; filename="extracted.mp3"');

        return $response;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        //すべてのExtractionを取得
        // $extractions = Extraction::orderBy('id')->get();
        
        //現在のユーザーがアップロードしたextractionsのみを取得
        $extractions = Extraction::whereHas('upload', function($query){
            $query->where('user_id', Auth::id());
        })->orderby('id')->get();

        return view('playlists.create', compact('extractions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Playlist $playlist)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Playlist $playlist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Playlist $playlist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Playlist $playlist)
    {
        //
    }
}
