<?php

namespace Modules\Categories\Domain\Interfaces;

use Modules\Categories\Domain\Entities\Category;
use Modules\Categories\Domain\ValueObjects\CategoryId;
use Modules\Categories\Domain\ValueObjects\CategorySlug;

interface CategoryRepositoryInterface
{
    public function save(Category $category): void;
    public function existsBySlug(CategorySlug $slug, ?CategoryId $ignoreId = null): bool;
}