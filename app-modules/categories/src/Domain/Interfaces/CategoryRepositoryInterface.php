<?php

namespace Modules\Categories\Domain\Interfaces;

use Modules\Categories\Domain\Entities\Category;

interface CategoryRepositoryInterface
{
    public function save(Category $category): void;
}
