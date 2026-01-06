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
        $q = TagModel::query()->where('slug', $slug->value());

        if ($ignoreId) {
            $q->where('id', '!=', $ignoreId->value());
        }

        return ! $q->exists();
    }
}
