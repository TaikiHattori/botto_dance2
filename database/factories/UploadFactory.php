<?php

namespace Database\Factories;

// 🔽 2行追加
use App\Models\Upload;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Upload>
 */
class UploadFactory extends Factory
{
    // 🔽 追加
  protected $model = Upload::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
                // 🔽 追加
            'user_id' => User::factory(), // UserモデルのFactoryを使用してユーザを生成
            'upload' => $this->faker->text(200) // ダミーのテキストデータ
        ];
    }
}
