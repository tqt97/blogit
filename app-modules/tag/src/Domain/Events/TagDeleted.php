<?php

declare(strict_types=1);

namespace Modules\Tag\Domain\Events;

use Modules\Tag\Domain\ValueObjects\TagId;

final class TagDeleted
{
    public function __construct(public readonly TagId $tagId) {}
}
