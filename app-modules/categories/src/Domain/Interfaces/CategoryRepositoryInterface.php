<?php

namespace Modules\Categories\Domain\Interfaces;

use Modules\Categories\Domain\Entities\CategoryEntity;
use Modules\Categories\Domain\ValueObjects\CategoryId;
use Modules\Categories\Domain\ValueObjects\CategorySlug;

interface CategoryRepositoryInterface
{
    public function save(CategoryEntity $category): CategoryEntity;

    // public function find(CategoryId $id): ?Category;

    // public function delete(CategoryId $id): void;

    public function existsBySlug(CategorySlug $slug, ?CategoryId $ignoreId = null): bool;
}
