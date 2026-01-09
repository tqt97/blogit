<?php

namespace Modules\Tag\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Tag\Application\QueryContracts\TagQueryRepository;
use Modules\Tag\Domain\Repositories\TagRepository;
use Modules\Tag\Domain\Services\TagSlugUniquenessChecker;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Mappers\TagMapper;
use Modules\Tag\Infrastructure\Persistence\Eloquent\ReadModels\EloquentTagReader;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Repositories\EloquentTagRepository;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Rules\EloquentUniqueTagSlugRule;

class TagServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TagMapper::class);

        $this->app->bind(TagQueryRepository::class, EloquentTagReader::class);
        $this->app->bind(TagRepository::class, EloquentTagRepository::class);
        $this->app->bind(TagSlugUniquenessChecker::class, EloquentUniqueTagSlugRule::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../Presentation/Routes/web.php');
    }
}
