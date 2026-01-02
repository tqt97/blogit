<?php

declare(strict_types=1);

namespace Modules\Tag\Infrastructure\Persistence\Eloquent\Repositories;

use LogicException;
use Modules\Tag\Domain\Entities\Tag;
use Modules\Tag\Domain\Repositories\TagRepositoryInterface;
use Modules\Tag\Domain\ValueObjects\TagId;
use Modules\Tag\Domain\ValueObjects\TagSlug;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Mappers\TagMapper;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Models\TagModel;

final class EloquentTagRepository implements TagRepositoryInterface
{
    public function __construct(private readonly TagMapper $mapper) {}

    public function nextIdentity(): TagId
    {
        // DDD thuần có thể generate UUID; với auto-increment thì identity đến sau khi save.
        // Trả "fake" ở đây không hợp. Thực tế với MySQL auto id, bạn có thể bỏ nextIdentity().
        // Mình giữ để đúng interface; có thể throw hoặc chuyển sang UUID.
        throw new LogicException('nextIdentity is not supported with auto-increment ids.');
    }

    public function save(Tag $tag): void
    {
        $model = null;

        if ($tag->id() !== null) {
            $model = TagModel::query()->find($tag->id()->value());
        }

        $model = $this->mapper->toPersistence($tag, $model);
        $model->save();

        // set id back to entity if new
        if ($tag->id() === null) {
            $tag->setId(new TagId((int) $model->id));
        }
    }

    public function getById(TagId $id): ?Tag
    {
        $model = TagModel::query()->find($id->value());

        return $model ? $this->mapper->toDomain($model) : null;
    }

    public function existsBySlug(TagSlug $slug, ?TagId $ignoreId = null): bool
    {
        $q = TagModel::query()->where('slug', $slug->value());

        if ($ignoreId !== null) {
            $q->where('id', '!=', $ignoreId->value());
        }

        return $q->exists();
    }

    public function delete(TagId $id): void
    {
        TagModel::query()->find($id->value())->delete();
    }
}
