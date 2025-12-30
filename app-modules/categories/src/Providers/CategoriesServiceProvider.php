<?php

namespace Modules\Categories\Providers;

use Illuminate\Support\ServiceProvider;

class CategoriesServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/categories-routes.php');
    }
}
