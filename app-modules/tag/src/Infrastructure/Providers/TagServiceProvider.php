<?php

namespace Modules\Tag\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Tag\Application\Contracts\TagReader;
use Modules\Tag\Domain\Repositories\TagRepository;
use Modules\Tag\Domain\Rules\UniqueTagSlugRule;
use Modules\Tag\Infrastructure\Adapters\EloquentTagReader;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Mappers\TagMapper;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Repositories\EloquentTagRepository;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Rules\EloquentUniqueTagSlugRule;

class TagServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TagMapper::class);

        $this->app->bind(TagReader::class, EloquentTagReader::class);
        $this->app->bind(TagRepository::class, EloquentTagRepository::class);
        $this->app->bind(UniqueTagSlugRule::class, EloquentUniqueTagSlugRule::class);
    }

    public function boot(): void {}
}
