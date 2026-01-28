<?php

namespace App\DataTransferObjects;

use App\Models\Article;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Lazy;

final class NewsArticleDto extends Data
{
    public function __construct(
        readonly public ?int $id,
        readonly public string $title,
        readonly public ?string $author,
        readonly public ?string $content,
        readonly public ?string $category,
        readonly public ?string $description,
        readonly public string $source,
        readonly public string $url,
        readonly public ?string $image,
        readonly public CarbonImmutable $published_at,
        #[DataCollectionOf(ArticleAuthorDto::class)]
        public DataCollection|Lazy|null $authors,
        #[DataCollectionOf(ArticleCategoryDto::class)]
        public DataCollection|Lazy|null $categories,
    ) {
    }

    public static function fromModel(Article $articleModel): self
    {
        $articleAttributes = $articleModel->toArray();
        
        return self::from([
            ...$articleAttributes,
            'published_at' => CarbonImmutable::parse($articleModel->published_at),
            'authors' => Lazy::whenLoaded('authors', $articleModel, 
                fn () => ArticleAuthorDto::collect($articleModel->authors)
            ),
            'categories' => Lazy::whenLoaded('categories', $articleModel, 
                fn () => ArticleCategoryDto::collect($articleModel->categories)
            ),
        ])->exclude('author', 'category');
    }
}
