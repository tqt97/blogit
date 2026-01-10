<?php

declare(strict_types=1);

namespace Modules\Tag\Infrastructure\Listeners;

use Illuminate\Cache\TaggableStore;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Events\Dispatcher;
use Modules\Tag\Domain\Events\TagCreated;
use Modules\Tag\Domain\Events\TagDeleted;
use Modules\Tag\Domain\Events\TagsBulkDeleted;
use Modules\Tag\Domain\Events\TagUpdated;

final class TagCacheInvalidator
{
    private readonly string $prefix;

    private readonly ?bool $useTags;

    public function __construct(private readonly CacheRepository $cache)
    {
        $this->prefix = (string) config('tag.cache.prefix');
        $this->useTags = config('tag.cache.use_tags');
    }

    public function handleTagCreation(TagCreated $event): void
    {
        $this->flushListCache();
    }

    public function handleTagUpdate(TagUpdated $event): void
    {
        $this->cache->forget("{$this->prefix}find:{$event->tag->id()->value()}");
        $this->flushListCache();
    }

    public function handleTagDeletion(TagDeleted $event): void
    {
        $this->cache->forget("{$this->prefix}find:{$event->tagId->value()}");
        $this->flushListCache();
    }

    public function handleTagsBulkDeletion(TagsBulkDeleted $event): void
    {
        foreach ($event->tagIds as $tagId) {
            $this->cache->forget("{$this->prefix}find:{$tagId->value()}");
        }
        $this->flushListCache();
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(TagCreated::class, [$this, 'handleTagCreation']);
        $events->listen(TagUpdated::class, [$this, 'handleTagUpdate']);
        $events->listen(TagDeleted::class, [$this, 'handleTagDeletion']);
        $events->listen(TagsBulkDeleted::class, [$this, 'handleTagsBulkDeletion']);
    }

    private function flushListCache(): void
    {
        if ($this->shouldUseTags()) {
            $this->cache->tags($this->prefix.'list')->flush();
        } else {
            $this->cache->forever($this->prefix.'list_version', time());
        }
    }

    private function shouldUseTags(): bool
    {
        if ($this->useTags === false) {
            return false;
        }

        return $this->cache->getStore() instanceof TaggableStore;
    }
}
