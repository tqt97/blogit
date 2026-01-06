<?php

declare(strict_types=1);

namespace Modules\Tag\Infrastructure\Persistence\Eloquent\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Tag\Domain\Entities\Tag;
use Modules\Tag\Domain\Repositories\TagRepository;
use Modules\Tag\Domain\ValueObjects\TagId;
use Modules\Tag\Domain\ValueObjects\TagSlug;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Mappers\TagMapper;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Models\TagModel;

final class EloquentTagRepository implements TagRepository
{
    public function __construct(private readonly TagMapper $mapper) {}

    public function save(Tag $tag): Tag
    {
        return DB::transaction(function () use ($tag): Tag {
            $model = $tag->id()
                ? TagModel::query()->findOrFail($tag->id()->value())
                : new TagModel;

            $this->mapper->toPersistence($tag, $model)->save();

            return $this->mapper->toEntity($model->fresh());
        });
    }

    public function getById(TagId $id): ?Tag
    {
        $model = TagModel::query()->find($id->value());

        return $model ? $this->mapper->toEntity($model) : null;
    }

    public function existsBySlug(TagSlug $slug, ?TagId $ignoreId = null): bool
    {
        $query = TagModel::query()->where('slug', $slug->value());

        if ($ignoreId !== null) {
            $query->where('id', '!=', $ignoreId->value());
        }

        return $query->exists();
    }

    public function delete(TagId $id): void
    {
        TagModel::query()->whereKey($id->value())->delete();
    }

    public function deleteMany(array $ids): void
    {
        $idValues = array_map(fn ($id) => $id->value(), $ids);

        TagModel::query()
            ->whereIn('id', $idValues)
            ->delete();
    }
}
