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
        Schema::table('uploads', function (Blueprint $table) {
            $table->integer('duration')->nullable()->after('mp3_url');
        });
        //新規カラム追加の際、Uploadモデルのfillableプロパティにも追加せよ
        //そうしないとテーブルにデータが保存されない
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uploads', function (Blueprint $table) {
            $table->dropColumn('duration');
        });
    }
};
