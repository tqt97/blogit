<?php

declare(strict_types=1);

namespace Modules\Categories\Infrastructure\Repositories;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Modules\Categories\Domain\Entities\CategoryEntity;
use Modules\Categories\Domain\Exceptions\CategoryNotFoundException;
use Modules\Categories\Domain\Exceptions\SlugAlreadyExistsException;
use Modules\Categories\Domain\Interfaces\CategoryRepositoryInterface;
use Modules\Categories\Domain\ValueObjects\CategoryId;
use Modules\Categories\Domain\ValueObjects\CategorySlug;
use Modules\Categories\Infrastructure\Persistence\Eloquent\Mappers\CategoryMapper;
use Modules\Categories\Infrastructure\Persistence\Eloquent\Models\CategoryModel;

final class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(private readonly CategoryMapper $mapper) {}

    public function save(CategoryEntity $category): CategoryEntity
    {
        try {
            $model = $category->id()
                ? CategoryModel::findOrFail($category->id()->value())
                : new CategoryModel;

            $this->mapper->toPersistence($category, $model)->save();

            return $this->mapper->toDomain($model);
        } catch (QueryException $e) {
            if ($this->isUniqueConstraintViolation($e)) {
                throw new SlugAlreadyExistsException($e);
            }

            throw $e;
        } catch (ModelNotFoundException) {
            throw new CategoryNotFoundException;
        }
    }

    public function existsBySlug(CategorySlug $slug, ?CategoryId $ignoreId = null): bool
    {
        $q = CategoryModel::query()->where('slug', $slug->value());

        if ($ignoreId !== null) {
            $q->where('id', '!=', $ignoreId->value());
        }

        return $q->exists();
    }

    private function isUniqueConstraintViolation(QueryException $e): bool
    {
        $sqlState = $e->errorInfo[0] ?? (string) $e->getCode();
        $errorCode = $e->errorInfo[1] ?? 0;

        if (in_array($sqlState, ['23505', '23000']) || $errorCode === 1062) {
            $message = strtolower($e->getMessage());

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
