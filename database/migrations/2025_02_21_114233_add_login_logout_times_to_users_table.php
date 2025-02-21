<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //新規作成時はusersテーブルにログイン＆ログアウト時間を保存できないのでnullable()を書いてエラーを防ぐ
            //User.phpの$fillableに追加せずにカラム追加できた
            $table->timestamp('last_login_at')->nullable()->after('email');
            $table->timestamp('last_logout_at')->nullable()->after('last_login_at');
            $table->string('total_usage_time')->nullable()->after('last_logout_at'); // アプリ累計使用時間
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_login_at');
            $table->dropColumn('last_logout_at');
            $table->dropColumn('total_usage_time');
        });
    }
};
