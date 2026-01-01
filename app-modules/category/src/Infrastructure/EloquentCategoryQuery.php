<?php

namespace Modules\Category\Infrastructure;

use Modules\Category\Models\Category;
use Modules\Shared\Contracts\Taxonomy\CategoryQuery;

class EloquentCategoryQuery implements CategoryQuery
{
    public function listForSelect(): array
    {
        return Category::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get()
            ->map(fn ($c) => [
                'id' => (int) $c->id,
                'label' => $c->name,
            ])
            ->all();
    }
}
