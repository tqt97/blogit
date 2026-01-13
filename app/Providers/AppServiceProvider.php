<?php

namespace App\Providers;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Eloquent strict mode
        if (! app()->isProduction()) {
            Model::shouldBeStrict();
        }

        // Slow query logging
        DB::whenQueryingForLongerThan(500, function (Connection $connection) {
            logger()->warning('Slow query detected', [
                'connection' => $connection->getName(),
            ]);
        });

        // Failed queue job logging
        Queue::failing(function (JobFailed $event) {
            logger()->error('Queue job failed', [
                'connection' => $event->connectionName,
                'job' => $event->job->resolveName(),
                'exception' => $event->exception->getMessage(),
            ]);
        });
    }
}
