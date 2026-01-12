<?php

declare(strict_types=1);

namespace Modules\Tag\Infrastructure\Providers;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Tag\Application\Ports\EventBus\EventBus;
use Modules\Tag\Application\Ports\ReadModels\TagReadModel;
use Modules\Tag\Application\Ports\Transaction\TransactionManager;
use Modules\Tag\Domain\Entities\Tag;
use Modules\Tag\Domain\Repositories\TagRepository;
use Modules\Tag\Infrastructure\Bus\Events\LaravelEventBus;
use Modules\Tag\Infrastructure\Listeners\TagCacheInvalidator;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Mappers\TagMapper;
use Modules\Tag\Infrastructure\Persistence\Eloquent\ReadModels\CachingTagReader;
use Modules\Tag\Infrastructure\Persistence\Eloquent\ReadModels\EloquentTagReader;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Repositories\EloquentTagRepository;
use Modules\Tag\Infrastructure\Transaction\DbTransactionManager;
use Modules\Tag\Presentation\Policies\TagPolicy;

class TagServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../../config/tag.php', 'tag');

        $this->app->singleton(TagMapper::class);

        // Application Ports
        $this->app->bind(TransactionManager::class, DbTransactionManager::class);
        $this->app->bind(EventBus::class, LaravelEventBus::class);
        $this->app->bind(TagReadModel::class, function ($app) {
            return new CachingTagReader(
                decorated: new EloquentTagReader,
                cache: $app->make(CacheRepository::class),
                ttl: (int) config('tag.cache.ttl'),
                useTags: config('tag.cache.use_tags'),
                prefix: config('tag.cache.prefix')
            );
        });

        // Domain Ports
        $this->app->bind(TagRepository::class, EloquentTagRepository::class);

        $this->app->bind(TagCacheInvalidator::class, function ($app) {
            return new TagCacheInvalidator(
                $app->make(CacheRepository::class),
                (string) config('tag.cache.prefix'),
                (bool) config('tag.cache.use_tags'),
            );
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../../config/tag.php' => config_path('tag.php'),
            ], 'tag-config');
        }

        $this->loadRoutesFrom(__DIR__.'/../../Presentation/Routes/web.php');

        Event::subscribe(TagCacheInvalidator::class);

        Gate::policy(Tag::class, TagPolicy::class);
    }
}
