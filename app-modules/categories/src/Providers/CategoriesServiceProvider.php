<?php

namespace Modules\Categories\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Categories\Domain\Interfaces\CategoryRepositoryInterface;
use Modules\Categories\Infrastructure\Repositories\EloquentCategoryRepository;

class CategoriesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            CategoryRepositoryInterface::class,
            EloquentCategoryRepository::class
        );
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/categories-routes.php');
    }
}
