<?php

namespace App\Services\News;

use App\DataTransferObjects\NewsArticleDto;
use App\Interfaces\NewsProviderInterface;
use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class NewsAggregatorService
{
    private Collection $providers;

    public function __construct()
    {
        $this->providers = collect();
    }

    public function addProvider(NewsProviderInterface $provider): self
    {
        $this->providers->push($provider);

        return $this;
    }

    public function fetchAndStore(): void
    {
        $this->providers->each(function (NewsProviderInterface $provider): void {
            $articles = $provider->fetchArticles();

            $articles->chunk(100)->each(function (Collection $chunk): void {
                $this->persistArticles($chunk);
            });
        });
    }

    private function persistArticles(Collection $articleDtos): Collection
    {
        return DB::transaction(function () use ($articleDtos) {
            $categoriesByName = $this->ensureCategoriesExist($articleDtos);
            $authorsByName = $this->ensureAuthorsExist($articleDtos);

            return $articleDtos->map(function (NewsArticleDto $dto) use ($authorsByName, $categoriesByName) {
                $articleAttributes = $this->dtoToArticleAttributes($dto);

                $article = Article::query()->updateOrCreate(
                    ['url' => $dto->url],
                    $articleAttributes
                );

                if (!empty($dto->author)) {
                    $authorIds = $this->resolveAuthorIds($dto->author, $authorsByName);
                    $article->authors()->sync($authorIds);
                }

                if (!empty($dto->category)) {
                    $category = $categoriesByName->firstWhere('name', Str::title($dto->category));
                    if ($category) {
                        $article->categories()->sync([$category->id]);
                    }
                }

                return $article->load('authors', 'categories');
            });
        });
    }

    private function dtoToArticleAttributes(NewsArticleDto $dto): array
    {
        $payload = $dto->except('id', 'author', 'category', 'authors', 'categories')->toArray();

        return [
            'title' => $payload['title'] ?? '',
            'description' => $payload['description'] ?? null,
            'content' => $payload['content'] ?? null,
            'url' => $payload['url'] ?? '',
            'image' => $payload['image'] ?? null,
            'source' => $payload['source'] ?? '',
            'published_at' => $payload['published_at'] ?? null,
        ];
    }

    private function ensureCategoriesExist(Collection $articleDtos): Collection
    {
        $names = $articleDtos
            ->pluck('category')
            ->map(fn ($value) => $value ? Str::title($value) : null)
            ->filter()
            ->unique()
            ->values();

        $existing = Category::query()->whereIn('name', $names)->get();

        $missingNames = $names->diff($existing->pluck('name'));

        if ($missingNames->isNotEmpty()) {
            $now = now();
            Category::query()->insert(
                $missingNames->map(fn ($name) => [
                    'name' => $name,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->toArray()
            );
            return Category::query()->whereIn('name', $names)->get();
        }

        return $existing;
    }

    private function ensureAuthorsExist(Collection $articleDtos): Collection
    {
        $names = $articleDtos
            ->pluck('author')
            ->filter()
            ->flatMap(fn ($value) => collect(explode(',', $value)))
            ->map(fn ($name) => Str::squish(Str::title(trim($name))))
            ->filter()
            ->unique()
            ->values();

        $existing = Author::query()->whereIn('name', $names)->get();

        $missingNames = $names->diff($existing->pluck('name'));

        if ($missingNames->isNotEmpty()) {
            $now = now();
            Author::query()->insert(
                $missingNames->map(fn ($name) => [
                    'name' => $name,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->toArray()
            );
            return Author::query()->whereIn('name', $names)->get();
        }

        return $existing;
    }

    private function resolveAuthorIds(string $authorString, Collection $authorsByName): array
    {
        return collect(explode(',', $authorString))
            ->map(fn ($name) => Str::squish(Str::title(trim($name))))
            ->map(fn ($name) => $authorsByName->firstWhere('name', $name)?->id)
            ->filter()
            ->values()
            ->toArray();
    }
}
