<?php

namespace Modules\Categories\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Categories\Domain\Interfaces\CategoryRepositoryInterface;
use Modules\Categories\Infrastructure\Persistence\Eloquent\Mappers\CategoryMapper;
use Modules\Categories\Infrastructure\Repositories\EloquentCategoryRepository;
use Modules\Category\Domain\Rules\UniqueCategorySlugRule;

class CategoriesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CategoryMapper::class);
        $this->app->bind(
            CategoryRepositoryInterface::class,
            EloquentCategoryRepository::class,
            UniqueCategorySlugRule::class,
        );
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/categories-routes.php');
    }
}
