<?php

use App\Models\Upload;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// 作成画面のテスト
it('displays the create upload page', function () {
  // テスト用のユーザーを作成
  $user = User::factory()->create();

  // ユーザーを認証（ログイン）
  $this->actingAs($user);

  // 作成画面にアクセス
  $response = $this->get('/uploads/create');

  // ステータスコードが200であることを確認
  $response->assertStatus(200);
});

// 作成処理のテスト
it('allows authenticated users to create a upload', function () {
  $user = User::factory()->create();
  $this->actingAs($user);

  // ストレージのモックを作成
  Storage::fake('s3');

  // アップロードデータを作成
  $uploadData = [
    'title' => 'test.mp3',
    'file' => UploadedFile::fake()->create('test.mp3', 1000, 'audio/mpeg'),
  ];

  // POSTリクエスト
  $response = $this->post('/uploads', $uploadData);

  // データベースに保存されたことを確認
  $this->assertDatabaseHas('uploads', [
    'title' => 'test.mp3',
  ]);

  // ストレージにファイルが保存されたことを確認
  Storage::disk('s3')->assertExists('uploads/test.mp3');

  // レスポンスの確認
  $response->assertStatus(302);
  $response->assertRedirect('/uploads');
});