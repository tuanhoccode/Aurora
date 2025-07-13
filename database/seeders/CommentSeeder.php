<?php

namespace Database\Seeders;

use App\Models\Comment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Comment::factory()->count(30)->create();

        // Tạo 20 reply cho các bình luận gốc
        $parentComments = Comment::inRandomOrder()->take(10)->get();

        foreach ($parentComments as $parent) {
            Comment::factory()->count(2)->create([
                'parent_id' => $parent->id,
                'product_id' => $parent->product_id,
            ]);
        }
    }
}
