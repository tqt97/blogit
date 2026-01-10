<?php

declare(strict_types=1);

namespace Modules\Tag\Domain\Events;

final readonly class TagsBulkDeleted
{
    public function __construct(public readonly array $tagIds) {}
}
