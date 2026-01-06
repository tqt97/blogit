<?php

declare(strict_types=1);

namespace Modules\Categories\Infrastructure\Repositories;

use Modules\Categories\Domain\Entities\Category;
use Modules\Categories\Domain\Interfaces\CategoryRepositoryInterface;
use Modules\Categories\Domain\ValueObjects\CategoryId;
use Modules\Categories\Domain\ValueObjects\CategorySlug;
use Modules\Categories\Infrastructure\Persistence\Eloquents\Models\CategoryModel;

final class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    public function save(Category $category): void
    {
        $model = $category->id()
            ? CategoryModel::findOrFail($category->id()->value())
            : new CategoryModel;

        $model->name = $category->name()->value();
        $model->slug = $category->slug()->value();
        $model->parent_id = $category->parentId()?->value();
        $model->description = $category->description()->value();
        $model->is_active = $category->status()->value();

        $model->save();

        if ($category->id() === null) {
            $category->setId(
                new CategoryId($model->id)
            );
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
}
