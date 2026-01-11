<?php

declare(strict_types=1);

namespace Modules\Tag\Infrastructure\Persistence\Eloquent\Mappers;

use Modules\Tag\Domain\Entities\Tag;
use Modules\Tag\Domain\ValueObjects\TagCreatedAt;
use Modules\Tag\Domain\ValueObjects\TagId;
use Modules\Tag\Domain\ValueObjects\TagName;
use Modules\Tag\Domain\ValueObjects\TagSlug;
use Modules\Tag\Domain\ValueObjects\TagUpdatedAt;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Models\TagModel;

final class TagMapper
{
    public function toEntity(TagModel $model): Tag
    {
        return Tag::reconstitute(
            id: new TagId((int) $model->id),
            name: new TagName((string) $model->name),
            slug: new TagSlug((string) $model->slug),
            createdAt: $model->created_at ? new TagCreatedAt($model->created_at->toISOString()) : null,
            updatedAt: $model->updated_at ? new TagUpdatedAt($model->updated_at->toISOString()) : null,
        );
    }

    public function toPersistence(Tag $tag, ?TagModel $model = null): TagModel
    {
        $model ??= new TagModel;

        $model->name = $tag->name()->value();
        $model->slug = $tag->slug()->value();

        return $model;
    }
}
