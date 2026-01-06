<?php

declare(strict_types=1);

namespace Modules\Tag\Domain\Rules;

use Modules\Tag\Domain\ValueObjects\TagId;
use Modules\Tag\Domain\ValueObjects\TagSlug;

interface UniqueTagSlugRule
{
    public function isUnique(TagSlug $slug, ?TagId $ignoreId = null): bool;
}
