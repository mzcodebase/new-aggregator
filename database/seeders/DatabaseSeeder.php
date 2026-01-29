<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $authors = Author::factory()->count(5)->create();
        $categories = Category::factory()->count(5)->create();

        Article::factory()
            ->count(20)
            ->create()
            ->each(function (Article $article) use ($authors, $categories): void {
                $article->authors()->attach(
                    $authors->random(rand(1, 2))->pluck('id')
                );
                $article->categories()->attach(
                    $categories->random(rand(1, 2))->pluck('id')
                );
            });
    }
}
