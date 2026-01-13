<?php

declare(strict_types=1);

namespace Modules\Categories\Infrastructure\Persistence\Eloquent\Mappers;

use Modules\Categories\Domain\Entities\CategoryEntity;
use Modules\Categories\Domain\ValueObjects\CategoryCreatedAt;
use Modules\Categories\Domain\ValueObjects\CategoryDescription;
use Modules\Categories\Domain\ValueObjects\CategoryId;
use Modules\Categories\Domain\ValueObjects\CategoryIsActive;
use Modules\Categories\Domain\ValueObjects\CategoryName;
use Modules\Categories\Domain\ValueObjects\CategoryParentId;
use Modules\Categories\Domain\ValueObjects\CategorySlug;
use Modules\Categories\Infrastructure\Persistence\Eloquent\Models\CategoryModel;

final class CategoryMapper
{
    public function toDomain(CategoryModel $model): CategoryEntity
    {
        return CategoryEntity::reconstitute(
            id: new CategoryId((int) $model->id),
            name: new CategoryName((string) $model->name),
            slug: new CategorySlug((string) $model->slug),
            description: $model->description ? new CategoryDescription((string) $model->description) : null,
            parentId: $model->parentId ? new CategoryParentId((int) $model->parentId) : null,
            isActive: CategoryIsActive::from((bool) $model->isActive),
            createdAt: $model->createdAt ? new CategoryCreatedAt($model->createdAt) : null,
        );
    }

    public function toPersistence(CategoryEntity $category, ?CategoryModel $model = null): CategoryModel
    {
        $model ??= new CategoryModel;

        $model->name = $category->name()->value();
        $model->slug = $category->slug()?->value();
        $model->parent_id = $category->parentId()?->value();
        $model->description = $category->description()?->value();
        $model->is_active = $category->status()->value();
        // createdAt is managed by Eloquent

        return $model;
    }
}
