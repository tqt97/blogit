<?php

declare(strict_types=1);

namespace Modules\Post\Application\Commands;

final readonly class SyncPostTagsCommand
{
    /** @param int[] $tagIds */
    public function __construct(
        public int $id,
        public array $tagIds
    ) {}
}
