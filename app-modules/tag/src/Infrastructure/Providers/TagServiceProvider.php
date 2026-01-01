<?php

namespace Modules\Tag\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Tag\Application\Contracts\TagReaderInterface;
use Modules\Tag\Domain\Repositories\TagRepositoryInterface;
use Modules\Tag\Infrastructure\Adapters\EloquentTagReader;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Mappers\TagMapper;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Repositories\EloquentTagRepository;

class TagServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // public read boundary
        $this->app->singleton(TagReaderInterface::class, EloquentTagReader::class);

        // internal CRUD repository
        $this->app->bind(TagRepositoryInterface::class, EloquentTagRepository::class);

        $this->app->singleton(TagMapper::class);

    }

    public function boot(): void {}
}
