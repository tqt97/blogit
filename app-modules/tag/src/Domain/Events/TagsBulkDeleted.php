<?php

declare(strict_types=1);

namespace Modules\Tag\Domain\Events;

use Modules\Tag\Domain\ValueObjects\TagIds;

final readonly class TagsBulkDeleted
{
    public function __construct(public TagIds $tagIds) {}
}
