<?php

declare(strict_types=1);

namespace Modules\Tag\Infrastructure\Persistence\Eloquent\Repositories;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Modules\Tag\Domain\Entities\Tag;
use Modules\Tag\Domain\Exceptions\SlugAlreadyExistsException;
use Modules\Tag\Domain\Exceptions\TagInUseException;
use Modules\Tag\Domain\Exceptions\TagNotFoundException;
use Modules\Tag\Domain\Repositories\TagRepository;
use Modules\Tag\Domain\ValueObjects\TagId;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Mappers\TagMapper;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Models\TagModel;

final class EloquentTagRepository implements TagRepository
{
    public function __construct(private readonly TagMapper $mapper) {}

    public function save(Tag $tag): Tag
    {
        try {
            $model = $tag->id()
                ? TagModel::query()->findOrFail($tag->id()->value())
                : new TagModel;

            $this->mapper->toPersistence($tag, $model)->save();

            return $this->mapper->toEntity($model->fresh());
        } catch (QueryException $e) {
            if ($this->isSlugUniqueViolation($e)) {
                throw new SlugAlreadyExistsException($e);
            }

            throw $e;
        } catch (ModelNotFoundException) {
            throw new TagNotFoundException;
        }
    }

    public function find(TagId $id): ?Tag
    {
        $model = TagModel::query()->find($id->value());

        return $model ? $this->mapper->toEntity($model) : null;
    }

    public function delete(TagId $id): void
    {
        try {
            TagModel::query()->whereKey($id->value())->delete();
        } catch (QueryException $e) {
            if ($this->isIntegrityConstraintViolation($e)) {
                throw new TagInUseException($e);
            }
            throw $e;
        }
    }

    public function deleteMany(array $ids): void
    {
        $idValues = array_map(fn (TagId $id) => $id->value(), $ids);

        try {
            TagModel::query()->whereIn('id', $idValues)->delete();
        } catch (QueryException $e) {
            if ($this->isIntegrityConstraintViolation($e)) {
                throw new TagInUseException($e);
            }
            throw $e;
        }
    }

    private function isSlugUniqueViolation(QueryException $e): bool
    {
        if (! $this->isIntegrityConstraintViolation($e)) {
            return false;
        }

        $message = strtolower($e->getMessage());

        return str_contains($message, 'slug');
    }

    private function isIntegrityConstraintViolation(QueryException $e): bool
    {
        // Works for MySQL/SQLite (23000) and Postgres (23505/23503)
        $sqlState = $e->errorInfo[0] ?? (string) $e->getCode();

        return in_array($sqlState, ['23000', '23505', '23503'], true);
    }
}
