<?php

namespace Modules\Tag\Infrastructure\Persistence\Eloquent\Rules;

use Modules\Tag\Domain\Rules\UniqueTagSlugRule;
use Modules\Tag\Domain\ValueObjects\TagId;
use Modules\Tag\Domain\ValueObjects\TagSlug;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Models\TagModel;

final class EloquentUniqueTagSlugRule implements UniqueTagSlugRule
{
    public function isUnique(TagSlug $slug, ?TagId $ignoreId = null): bool
    {
        $query = TagModel::query()->where('slug', $slug->value());

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId->value());
        }

        return ! $query->exists();
    }
}
