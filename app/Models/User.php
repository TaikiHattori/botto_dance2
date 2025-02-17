<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function uploads()
  {
    return $this->hasMany(Upload::class);
  }

//-------------------------------------------
//ユーザー削除したら、そのユーザーのS3UP曲も削除
//-------------------------------------------

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user){
        //ユーザーが削除される前に、そのユーザーのS3UP曲を削除（フック設定）
        $user->deleteS3Objects();
        });
    }

    public function deleteS3Objects()
    {
        //ユーザーのS3UP曲を取得
        $uploads = $this->uploads;

        foreach($uploads as $upload) {
            $upload->deleteS3Object();
        }
    }
}
