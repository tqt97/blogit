<?php

declare(strict_types=1);

namespace Modules\Tag\Domain\Events;

use Modules\Tag\Domain\ValueObjects\TagId;
use Modules\Tag\Domain\ValueObjects\TagName;
use Modules\Tag\Domain\ValueObjects\TagSlug;

final readonly class TagUpdated
{
    public function __construct(
        public TagId $id,
        public TagName $name,
        public TagSlug $slug,
    ) {}
}
