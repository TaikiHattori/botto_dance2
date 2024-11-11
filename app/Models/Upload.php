<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    /** @use HasFactory<\Database\Factories\UploadFactory> */
    use HasFactory;

     protected $fillable = ['title','mp3_url',];

    public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
     * リレーションシップの定義：Uploadは複数のExtractionを持つ
     */
    public function extractions()
    {
      return $this->hasMany(Extraction::class);
    }
}
