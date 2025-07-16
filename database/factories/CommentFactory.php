<?php

namespace Database\Factories;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::inRandomOrder()->first()->id ?? 1,
            'user_id' => User::inRandomOrder()->first()->id ?? 1,
            'parent_id' => null, // để sau có thể tạo reply
            'content' => $this->faker->sentence(10),
            'reason' => null,
            'is_active' => $this->faker->boolean(80), // 80% bình luận được duyệt
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
