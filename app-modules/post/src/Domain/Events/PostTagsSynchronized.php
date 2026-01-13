<?php

declare(strict_types=1);

namespace Modules\Post\Domain\Events;

final readonly class PostTagsSynchronized
{
    /** @param int[] $tagIds */
    public function __construct(
        public int $postId,
        public array $tagIds
    ) {}
}
