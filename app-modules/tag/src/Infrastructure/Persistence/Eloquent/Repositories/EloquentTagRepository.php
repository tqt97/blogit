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
use Modules\Tag\Domain\ValueObjects\TagIds;
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

            if ($tag->id()) {
                return $tag;
            }

            return $tag->withId(new TagId((int) $model->id));
        } catch (QueryException $e) {
            if ($this->isUniqueConstraintViolation($e)) {
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
            $count = TagModel::query()->whereKey($id->value())->delete();

            if ($count === 0) {
                throw new TagNotFoundException;
            }
        } catch (QueryException $e) {
            if ($this->isForeignKeyConstraintViolation($e)) {
                throw new TagInUseException($e);
            }
            throw $e;
        }
    }

    public function deleteMany(TagIds $ids): void
    {
        $idValues = $ids->toScalars();

        try {
            TagModel::query()->whereIn('id', $idValues)->delete();
        } catch (QueryException $e) {
            if ($this->isForeignKeyConstraintViolation($e)) {
                throw new TagInUseException($e);
            }
            throw $e;
        }
    }

    private function isUniqueConstraintViolation(QueryException $e): bool
    {
        // 23505 (Postgres) or 1062 (MySQL) or 23000 (SQLite/Generic)
        $sqlState = $e->errorInfo[0] ?? (string) $e->getCode();
        $errorCode = $e->errorInfo[1] ?? 0;

        if (in_array($sqlState, ['23505', '23000']) || $errorCode === 1062) {
            $message = strtolower($e->getMessage());

            // Check generic/common indicators
            if (str_contains($message, 'duplicate entry') ||
                str_contains($message, 'unique constraint') ||
                str_contains($message, 'tags_slug_unique')) {
                return true;
            }
        }

        return false;
    }

    private function isForeignKeyConstraintViolation(QueryException $e): bool
    {
        // 23503 (Postgres/Generic) or 1451/1452 (MySQL)
        $sqlState = $e->errorInfo[0] ?? (string) $e->getCode();
        $errorCode = $e->errorInfo[1] ?? 0;

        if ($sqlState === '23503') {
            return true;
        }

        // MySQL FK Error
        if (in_array($errorCode, [1451, 1452])) {
            return true;
        }

        // SQLite FK error often 23000 with specific message, but usually 23503 if extended result codes used
        // or just strict check on 23503 which is standard ANSI SQL state for FK violation

        return false;
    }
}
