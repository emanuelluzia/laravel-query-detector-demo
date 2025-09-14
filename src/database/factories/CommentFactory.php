<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
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
    protected $model = Comment::class;
    public function definition(): array
    {
        return [
            'post_id' => Post::query()->inRandomOrder()->value('id') ?? Post::factory(),
            'user_id' => User::query()->inRandomOrder()->value('id') ?? User::factory(),
            'body'    => $this->faker->sentence(),
        ];
    }
}
