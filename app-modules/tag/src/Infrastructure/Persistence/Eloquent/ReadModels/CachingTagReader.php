<?php

declare(strict_types=1);

namespace Modules\Tag\Infrastructure\Persistence\Eloquent\ReadModels;

use Illuminate\Cache\TaggableStore;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Tag\Application\DTOs\TagDTO;
use Modules\Tag\Application\QueryContracts\TagQueryRepository;
use Modules\Tag\Domain\ValueObjects\Pagination;
use Modules\Tag\Domain\ValueObjects\SearchTerm;
use Modules\Tag\Domain\ValueObjects\Sorting;

final class CachingTagReader implements TagQueryRepository
{
    public function __construct(
        private readonly EloquentTagReader $decorated,
        private readonly CacheRepository $cache,
        private readonly int $ttl,
        private readonly ?bool $useTags,
        private readonly string $prefix,
    ) {}

    public function find(int $id): ?TagDTO
    {
        $key = "{$this->prefix}find:{$id}";

        return $this->cache->remember($key, $this->ttl, fn () => $this->decorated->find($id));
    }

    public function paginate(?SearchTerm $search, Pagination $pagination, Sorting $sorting): LengthAwarePaginator
    {
        $key = $this->buildPaginateCacheKey($search, $pagination, $sorting);

        $cache = $this->shouldUseTags()
            ? $this->cache->tags($this->prefix.'list')
            : $this->cache;

        return $cache->remember($key, $this->ttl, function () use ($search, $pagination, $sorting) {
            return $this->decorated->paginate($search, $pagination, $sorting);
        });
    }

    private function buildPaginateCacheKey(?SearchTerm $search, Pagination $pagination, Sorting $sorting): string
    {
        $searchKey = $search?->value ?? 'none';
        $pageKey = "page={$pagination->page}";
        $perPageKey = "per_page={$pagination->perPage}";
        $sortKey = "sort={$sorting->field->value}";
        $directionKey = "direction={$sorting->direction->value}";

        $key = "{$this->prefix}paginate:{$searchKey}:{$pageKey}:{$perPageKey}:{$sortKey}:{$directionKey}";

        if (! $this->shouldUseTags()) {
            $version = $this->cache->get($this->prefix.'list_version', '0');

            return "{$key}:v{$version}";
        }

        return $key;
    }

    private function shouldUseTags(): bool
    {
        if ($this->useTags === false) {
            return false;
        }

        return $this->cache->getStore() instanceof TaggableStore;
    }
}
