<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use App\Models\Extraction;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class ExtractionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // ログインユーザーのアップロードを取得（降順）
        $uploads = Auth::user()->uploads()->orderBy('created_at', 'desc');
        
        // 検索クエリが存在する場合、フィルタリング
        if ($request->has('search')) {
            $search = $request->input('search');
            $uploads->where('title', 'LIKE', "%{$search}%");
        }

        $uploads = $uploads->get();

        // アップロードに関連するすべての抽出を取得
        $extractions = $uploads->flatMap(function ($upload) {
            return $upload->extractions;
        });

        // 現在のユーザーに紐づくupload_idに紐づくextraction_idの数を取得
        $getCountId = $extractions->count();

        return view('extractions.index', compact('extractions', 'getCountId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($upload_id)
    {
        // upload_idからアップロードを取得
        $upload = Upload::findOrFail($upload_id);
        //dd($upload);
        
        $title = $upload->title;
        $duration = $upload->duration;

        return view('extractions.create', compact('upload', 'title', 'duration'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
        'start_minu' => 'required|integer|min:0',
        'start_sec' => 'required|integer|min:0|max:59',
        'end_minu' => 'required|integer|min:0',
        'end_sec' => 'required|integer|min:0|max:59',
        'upload_id' => 'required|exists:uploads,id',
    ]);

    //分と秒をそのまま「00：分：秒」形式に変換
    $start_time = sprintf('00:%02d:%02d', $request->start_minu, $request->start_sec);
    $end_time   = sprintf('00:%02d:%02d', $request->end_minu, $request->end_sec);
    
    //dd($start_time, $end_time);

    // データベースに保存
    $extraction = new Extraction();
    $extraction->upload_id = $request->upload_id;
    $extraction->start = $start_time;
    $extraction->end = $end_time;
    $extraction->save();

        return redirect()->route('extractions.index')->with('success', 'Extraction data saved successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Extraction $extraction)
    {
        return view('extractions.show', compact('extraction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Extraction $extraction)
    {
        return view('extractions.edit',compact('extraction'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Extraction $extraction)
    {
        $request->validate([
            'start_minu' => 'required|integer|min:0',
            'start_sec'  => 'required|integer|min:0|max:59',
            'end_minu'   => 'required|integer|min:0',
            'end_sec'    => 'required|integer|min:0|max:59',
        ]);

        // 分と秒をそのまま「00：分：秒」形式に変換
        $start_time = sprintf('00:%02d:%02d', $request->start_minu, $request->start_sec);
        $end_time   = sprintf('00:%02d:%02d', $request->end_minu, $request->end_sec);

        $extraction->update([
            'start' => $start_time,
            'end' => $end_time,
        ]);

        return redirect()->route('extractions.show',$extraction);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Extraction $extraction)
    {
        $extraction->delete();

    return redirect()->route('extractions.index');
    }


    public function bulkDelete(Request $request)
    {

        Log::info('Request: ',  $request->all());

        $ids = $request->input('extractions');
        Log::info('Ids to delete: ' , ['ids' => $ids]);

        if ($ids) {
        Extraction::whereIn('id', $ids)->delete();
        }
        return redirect()->route('extractions.index');
    }
}
