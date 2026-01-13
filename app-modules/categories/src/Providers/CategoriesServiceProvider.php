<?php

namespace Modules\Categories\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Categories\Application\Port\EventBus\EventBus;
use Modules\Categories\Application\Port\Transaction\TransactionManager;
use Modules\Categories\Domain\Interfaces\CategoryRepositoryInterface;
use Modules\Categories\Infrastructure\Bus\Events\LaravelEventBus;
use Modules\Categories\Infrastructure\Persistence\Eloquent\Mappers\CategoryMapper;
use Modules\Categories\Infrastructure\Repositories\EloquentCategoryRepository;
use Modules\Categories\Infrastructure\Transaction\DbTransactionManager;
use Modules\Category\Domain\Rules\UniqueCategorySlugRule;

class CategoriesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CategoryMapper::class);
        $this->app->bind(EventBus::class, LaravelEventBus::class);
        $this->app->bind(TransactionManager::class, DbTransactionManager::class);
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
