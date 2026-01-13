<?php

declare(strict_types=1);

namespace Modules\Post\Application\DTOs;

final readonly class PostDTO
{
    /**
     * @param  \Modules\Tag\Application\DTOs\TagDTO[]  $tags
     */
    public function __construct(
        public int $id,
        public string $title,
        public string $slug,
        public ?string $excerpt,
        public string $content,
        public string $status,
        public ?string $publishedAt,
        public int $viewCount,
        public int $commentCount,
        public int $likeCount,
        public string $createdAt,
        public string $updatedAt,
        public array $tags = [],
    ) {}
}
