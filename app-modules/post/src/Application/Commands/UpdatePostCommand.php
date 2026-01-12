<?php

declare(strict_types=1);

namespace Modules\Post\Application\Commands;

final readonly class UpdatePostCommand
{
    /** @param int[] $tagIds */
    public function __construct(
        public int $id,
        public int $userId,
        public int $categoryId,
        public string $title,
        public string $slug,
        public string $excerpt,
        public string $content,
        public string $status,
        public ?string $publishedAt,
        public array $tagIds
    ) {}
}
