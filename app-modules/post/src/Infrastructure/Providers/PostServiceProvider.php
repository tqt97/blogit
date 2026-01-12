<?php

namespace Modules\Post\Infrastructure\Providers;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Post\Application\Ports\EventBus\EventBus;
use Modules\Post\Application\Ports\ReadModels\PostReadModel;
use Modules\Post\Application\Ports\Transaction\TransactionManager;
use Modules\Post\Domain\Entities\Post;
use Modules\Post\Domain\Repositories\PostRepository;
use Modules\Post\Infrastructure\Bus\Events\LaravelEventBus;
use Modules\Post\Infrastructure\Listeners\PostCacheInvalidator;
use Modules\Post\Infrastructure\Persistence\Eloquent\Mappers\PostMapper;
use Modules\Post\Infrastructure\Persistence\Eloquent\ReadModels\CachingPostReader;
use Modules\Post\Infrastructure\Persistence\Eloquent\ReadModels\EloquentPostReader;
use Modules\Post\Infrastructure\Persistence\Eloquent\Repositories\EloquentPostRepository;
use Modules\Post\Infrastructure\Transaction\DbTransactionManager;
use Modules\Post\Presentation\Policies\PostPolicy;
use Modules\Tag\Application\Ports\ReadModels\TagReadModel;

class PostServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../../config/post.php', 'post');
        $this->app->singleton(PostMapper::class);

        // Application Ports
        $this->app->bind(TransactionManager::class, DbTransactionManager::class);
        $this->app->bind(EventBus::class, LaravelEventBus::class);

        $this->app->bind(EloquentPostReader::class, function ($app) {
            return new EloquentPostReader(
                $app->make(TagReadModel::class)
            );
        });

        $this->app->bind(PostReadModel::class, function ($app) {
            return new CachingPostReader(
                decorated: $app->make(EloquentPostReader::class),
                cache: $app->make(CacheRepository::class),
                ttl: (int) config('post.cache.ttl'),
                useTags: config('post.cache.use_tags'),
                prefix: config('post.cache.prefix')
            );
        });

        // Domain Ports
        $this->app->bind(PostRepository::class, EloquentPostRepository::class);

        $this->app->bind(PostCacheInvalidator::class, function ($app) {
            return new PostCacheInvalidator(
                $app->make(CacheRepository::class),
                (string) config('post.cache.prefix'),
                (bool) config('post.cache.use_tags'),
            );
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../../config/post.php' => config_path('post.php'),
            ], 'post-config');
        }

        $this->loadRoutesFrom(__DIR__.'/../../Presentation/Routes/web.php');

        Event::subscribe(PostCacheInvalidator::class);

        Gate::policy(Post::class, PostPolicy::class);
    }
}
