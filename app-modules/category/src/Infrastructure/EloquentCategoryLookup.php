<?php

namespace Modules\Category\Infrastructure;

use Illuminate\Validation\ValidationException;
use Modules\Category\Models\Category;
use Modules\Shared\Contracts\Taxonomy\CategoryLookup;

class EloquentCategoryLookup implements CategoryLookup
{
    public function exists(int $id): bool
    {
        return Category::query()->whereKey($id)->exists();
    }

    public function assertExists(int $id): void
    {
        if (! $this->exists($id)) {
            throw ValidationException::withMessages([
                'category_id' => 'Category does not exist.',
            ]);
        }
    }
}
