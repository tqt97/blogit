<?php

namespace Modules\Post\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Post\Infrastructure\Persistence\Eloquent\Models\PostModel;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<PostModel>
 */
class PostModelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PostModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $title = fake()->sentence(),
            'slug' => Str::slug($title),
            'excerpt' => fake()->sentence(),
            'content' => fake()->paragraphs(2, true),
            'user_id' => 1,
            'category_id' => 1,
            'published_at' => now(),
            'status' => 'published',
            'views_count' => 0,
            'comments_count' => 0,
            'likes_count' => 0,
        ];
    }
}
