<?php

namespace Modules\AccessControls\Providers;

use Illuminate\Support\ServiceProvider;

class AccessControlsServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/access-controls-routes.php');
    }
}