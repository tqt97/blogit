<?php

namespace Modules\Tag\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Tag\Application\Contracts\TagReader;
use Modules\Tag\Domain\Repositories\TagRepository;
use Modules\Tag\Infrastructure\Adapters\EloquentTagReader;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Mappers\TagMapper;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Repositories\EloquentTagRepository;

class TagServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TagReader::class, EloquentTagReader::class);
        $this->app->bind(TagRepository::class, EloquentTagRepository::class);
        $this->app->singleton(TagMapper::class);
    }

    public function boot(): void {}
}
