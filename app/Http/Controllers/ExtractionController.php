<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use App\Models\Extraction;
use Illuminate\Http\Request;

// 🔽 追加
use Illuminate\Support\Facades\Auth;

class ExtractionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 🔽 追加
        $uploads = Auth::user()->uploads;
        
        // すべてのアップロードに関連するすべての抽出を取得
        $extractions = $uploads->flatMap(function ($upload) {
            return $upload->extractions;
        });

        //dd($extractions);
        
        return view('extractions.index', compact('extractions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($upload_id)
    {
        // upload_idからアップロードを取得
        $upload = Upload::findOrFail($upload_id);
        //dd($upload);
        
        return view('extractions.create', compact('upload'));
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
}
