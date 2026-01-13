<?php

declare(strict_types=1);

namespace Modules\Tag\Infrastructure\Persistence\Eloquent\ReadModels;

use Illuminate\Cache\TaggableStore;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Modules\Tag\Application\DTOs\TagDTO;
use Modules\Tag\Application\Ports\ReadModels\TagReadModel;
use Modules\Tag\Domain\ValueObjects\Pagination;
use Modules\Tag\Domain\ValueObjects\SearchTerm;
use Modules\Tag\Domain\ValueObjects\Sorting;

final class CachingTagReader implements TagReadModel
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

    public function getByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        // Simple implementation: fetch individually from cache or delegated reader
        // A optimized multi-get could be implemented but let's stick to correctness first.
        // Or better: delegate to decorated reader if we don't want to complex cache logic for multi-get right now.
        // For strict caching, we should map each ID to a key.

        // Let's rely on decorated reader for now as multi-get caching is complex to implement robustly without proper mget support or looping.
        // Loop approach:
        $results = [];
        foreach ($ids as $id) {
            $dto = $this->find($id);
            if ($dto) {
                $results[] = $dto;
            }
        }

        return $results;
    }

    public function paginate(?SearchTerm $search, Pagination $pagination, Sorting $sorting): LengthAwarePaginator
    {
        $key = $this->buildPaginateCacheKey($search, $pagination, $sorting);

        $cache = $this->shouldUseTags()
            ? $this->cache->tags($this->prefix.'list')
            : $this->cache;

        $data = $cache->remember($key, $this->ttl, function () use ($search, $pagination, $sorting) {
            $paginator = $this->decorated->paginate($search, $pagination, $sorting);

            return [
                'items' => $paginator->items(),
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
            ];
        });

        return new LengthAwarePaginator(
            $data['items'],
            $data['total'],
            $data['per_page'],
            $data['current_page'],
            ['path' => Paginator::resolveCurrentPath()]
        );
    }

    private function buildPaginateCacheKey(?SearchTerm $search, Pagination $pagination, Sorting $sorting): string
    {
        $searchHash = $this->hashSearch($search?->value);
        $pageKey = "page={$pagination->page}";
        $perPageKey = "per_page={$pagination->perPage}";
        $sortKey = "sort={$sorting->field->value}";
        $directionKey = "direction={$sorting->direction->value}";

        $key = "{$this->prefix}paginate:{$searchHash}:{$pageKey}:{$perPageKey}:{$sortKey}:{$directionKey}";

        if (! $this->shouldUseTags()) {
            $version = $this->cache->get($this->prefix.'list_version', '0');

            return "{$key}:v{$version}";
        }

        return $key;
    }

    private function hashSearch(?string $value): string
    {
        $normalized = trim(mb_strtolower((string) $value));
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;

        // empty search => stable value
        if ($normalized === '') {
            return 'none';
        }

        return sha1($normalized);
    }

    private function shouldUseTags(): bool
    {
        if ($this->useTags === false) {
            return false;
        }

        return $this->cache->getStore() instanceof TaggableStore;
    }
}
