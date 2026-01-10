<?php

declare(strict_types=1);

namespace Modules\Tag\Infrastructure\Persistence\Eloquent\Repositories;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Modules\Tag\Domain\Entities\Tag;
use Modules\Tag\Domain\Events\TagCreated;
use Modules\Tag\Domain\Events\TagDeleted;
use Modules\Tag\Domain\Events\TagsBulkDeleted;
use Modules\Tag\Domain\Events\TagUpdated;
use Modules\Tag\Domain\Exceptions\SlugAlreadyExistsException;
use Modules\Tag\Domain\Exceptions\TagNotFoundException;
use Modules\Tag\Domain\Repositories\TagRepository;
use Modules\Tag\Domain\ValueObjects\TagId;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Mappers\TagMapper;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Models\TagModel;

final class EloquentTagRepository implements TagRepository
{
    /** @var string ANSI SQLSTATE for Integrity Constraint Violation */
    private const SQL_INTEGRITY_VIOLATION = '23000';

    public function __construct(private readonly TagMapper $mapper) {}

    public function save(Tag $tag): Tag
    {
        $isNew = $tag->id() === null;

        try {
            $savedTag = DB::transaction(function () use ($tag, $isNew): Tag {
                try {
                    $model = $isNew
                        ? new TagModel
                        : TagModel::query()->findOrFail($tag->id()->value());
                } catch (ModelNotFoundException) {
                    throw new TagNotFoundException;
                }

                $this->mapper->toPersistence($tag, $model)->save();

                return $this->mapper->toEntity($model->fresh());
            });
        } catch (QueryException $e) {
            throw_if(
                $e->getCode() === self::SQL_INTEGRITY_VIOLATION,
                SlugAlreadyExistsException::class
            );

            throw $e;
        }

        DB::afterCommit(function () use ($savedTag, $isNew) {
            Event::dispatch($isNew ? new TagCreated($savedTag) : new TagUpdated($savedTag));
        });

        return $savedTag;
    }

    public function getById(TagId $id): ?Tag
    {
        $model = TagModel::query()->find($id->value());

        return $model ? $this->mapper->toEntity($model) : null;
    }

    public function delete(TagId $id): void
    {
        if (TagModel::query()->whereKey($id->value())->delete()) {
            DB::afterCommit(fn () => Event::dispatch(new TagDeleted($id)));
        }
    }

    public function deleteMany(array $ids): void
    {
        $idValues = array_map(fn (TagId $id) => $id->value(), $ids);

        if (TagModel::query()->whereIn('id', $idValues)->delete()) {
            DB::afterCommit(fn () => Event::dispatch(new TagsBulkDeleted($ids)));
        }
    }
}
