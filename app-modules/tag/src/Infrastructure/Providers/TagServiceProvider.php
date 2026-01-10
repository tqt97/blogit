<?php

declare(strict_types=1);

namespace Modules\Tag\Infrastructure\Providers;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Tag\Application\QueryContracts\TagQueryRepository;
use Modules\Tag\Domain\Entities\Tag;
use Modules\Tag\Domain\Repositories\TagRepository;
use Modules\Tag\Infrastructure\Listeners\TagCacheInvalidator;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Mappers\TagMapper;
use Modules\Tag\Infrastructure\Persistence\Eloquent\ReadModels\CachingTagReader;
use Modules\Tag\Infrastructure\Persistence\Eloquent\ReadModels\EloquentTagReader;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Repositories\EloquentTagRepository;
use Modules\Tag\Presentation\Policies\TagPolicy;

class TagServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../../config/tag.php', 'tag');

        $this->app->singleton(TagMapper::class);

        $this->app->bind(TagQueryRepository::class, function ($app) {
            return new CachingTagReader(
                decorated: new EloquentTagReader,
                cache: $app->make(CacheRepository::class),
                ttl: (int) config('tag.cache.ttl'),
                useTags: config('tag.cache.use_tags'),
                prefix: config('tag.cache.prefix')
            );
        });
        $this->app->bind(TagRepository::class, EloquentTagRepository::class);
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
