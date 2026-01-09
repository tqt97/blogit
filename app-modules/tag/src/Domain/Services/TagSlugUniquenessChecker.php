<?php

declare(strict_types=1);

namespace Modules\Tag\Domain\Services;

use Modules\Tag\Domain\ValueObjects\TagId;
use Modules\Tag\Domain\ValueObjects\TagSlug;

interface TagSlugUniquenessChecker
{
    public function isUnique(TagSlug $slug, ?TagId $ignoreId = null): bool;
}
