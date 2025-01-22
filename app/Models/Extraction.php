<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Extraction extends Model
{
    /** @use HasFactory<\Database\Factories\ExtractionFactory> */
    use HasFactory;

    protected $fillable = [
        'start',
        'end',
    ];

     /**
     * リレーションシップの定義：Extractionは1つのUploadに属する
     */
    public function upload()
    {
        return $this->belongsTo(Upload::class);
    }
}
