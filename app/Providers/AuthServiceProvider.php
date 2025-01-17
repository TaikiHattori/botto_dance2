<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as BaseAuthServiceProvider; // エイリアスを使用
use App\Models\User;
use App\Policies\UploadPolicy;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends BaseAuthServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    // protected $policies = [
    //     User::class => UploadPolicy::class,
    // ];

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // uploadという名前のゲートを定義して、
        //そのゲートの認可ロジックとしてUploadPolicyクラスのcreateメソッドを使用する
        Gate::define('upload', [UploadPolicy::class, 'create']);
    }
}
