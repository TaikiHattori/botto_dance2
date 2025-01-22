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
        'start_time' => 'required|integer',
        'end_time' => 'required|integer',
        'upload_id' => 'required|exists:uploads,id',
    ]);

    // データベースに保存
    $extraction = new Extraction();
    $extraction->upload_id = $request->upload_id;
    $extraction->start = gmdate("H:i:s", $request->start_time); // 秒数を時:分:秒に変換
    $extraction->end = gmdate("H:i:s", $request->end_time); // 秒数を時:分:秒に変換
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
            'start' => 'required|integer|min:0',
            'end' => 'required|integer|min:0',
        ]);

        // 数値を適切な時間形式に変換
        $start = gmdate('H:i:s', $request->input('start'));
        $end = gmdate('H:i:s', $request->input('end'));

        $extraction->update([
            'start' => $start,
            'end' => $end,
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
