<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Log;

class Upload extends Model
{
    /** @use HasFactory<\Database\Factories\UploadFactory> */
    use HasFactory;

    //テーブルに新規カラム追加した際に、$fillable必要
    protected $fillable = ['title','mp3_url','duration','genre',];

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

  //-------------------------------------------
  //ユーザー削除したら、そのユーザーのS3UP曲も削除
  //-------------------------------------------
  public function deleteS3Object()
  {
    try {
      //S3クライアントの設定
      $s3Config = [
                'version' => 'latest',
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
            $key = parse_url($this->mp3_url, PHP_URL_PATH);
            $key = ltrim($key, '/');
            $key = urldecode($key);

            //AWS SDK for PHPでdeleteObjectメソッドを呼び出す
            $s3->deleteObject([
              'Bucket' => $bucket,
              'Key' => $key,
            ]);
            Log::info('S3 object deleted', ['key' => $key]);

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
