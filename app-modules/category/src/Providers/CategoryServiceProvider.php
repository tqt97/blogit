<?php

namespace Modules\Category\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Category\Infrastructure\EloquentCategoryLookup;
use Modules\Category\Infrastructure\EloquentCategoryQuery;
use Modules\Shared\Contracts\Taxonomy\CategoryLookup;
use Modules\Shared\Contracts\Taxonomy\CategoryQuery;

class CategoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CategoryLookup::class, EloquentCategoryLookup::class);
        $this->app->bind(CategoryQuery::class, EloquentCategoryQuery::class);
    }

    public function boot(): void {}
}
