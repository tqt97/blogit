<?php

namespace Modules\Tag\Infrastructure\Persistence\Eloquent\Rules;

use Modules\Tag\Domain\Services\TagSlugUniquenessChecker;
use Modules\Tag\Domain\ValueObjects\TagId;
use Modules\Tag\Domain\ValueObjects\TagSlug;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Models\TagModel;

final class EloquentUniqueTagSlugRule implements TagSlugUniquenessChecker
{
    public function isUnique(TagSlug $slug, ?TagId $ignoreId = null): bool
    {
        return ! TagModel::query()
            ->where('slug', $slug->value())
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId->value()))
            ->exists();
    }
}
