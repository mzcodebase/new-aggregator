<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'content' => fake()->paragraphs(3, true),
            'source' => fake()->company(),
            'url' => fake()->unique()->url(),
            'image' => fake()->imageUrl(640, 480, 'business', true),
            'published_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
