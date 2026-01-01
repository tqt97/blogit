<?php
declare(strict_types=1);

namespace Modules\Tag\Infrastructure\Persistence\Eloquent\Mappers;

use Modules\Tag\Domain\Entities\Tag;
use Modules\Tag\Domain\ValueObjects\TagId;
use Modules\Tag\Domain\ValueObjects\TagName;
use Modules\Tag\Domain\ValueObjects\TagSlug;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Models\TagModel;

final class TagMapper
{
    public function toDomain(TagModel $model): Tag
    {
        return new Tag(
            id: new TagId((int) $model->id),
            name: new TagName((string) $model->name),
            slug: new TagSlug((string) $model->slug),
        );
    }

    public function toPersistence(Tag $tag, ?TagModel $model = null): TagModel
    {
        $model ??= new TagModel();

        $model->name = $tag->name()->value();
        $model->slug = $tag->slug()->value();

        return $model;
    }
}
