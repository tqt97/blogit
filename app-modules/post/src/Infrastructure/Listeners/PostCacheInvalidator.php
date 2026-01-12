<?php

declare(strict_types=1);

namespace Modules\Post\Infrastructure\Listeners;

use Illuminate\Cache\TaggableStore;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Events\Dispatcher;
use Modules\Post\Domain\Events\PostCreated;
use Modules\Post\Domain\Events\PostDeleted;
use Modules\Post\Domain\Events\PostsBulkDeleted;
use Modules\Post\Domain\Events\PostTagsSynchronized;
use Modules\Post\Domain\Events\PostUpdated;

final class PostCacheInvalidator
{
    private readonly string $prefix;

    private readonly ?bool $useTags;

    public function __construct(
        private readonly CacheRepository $cache,
        string $prefix,
        bool $useTags,
    ) {
        $this->prefix = $prefix;
        $this->useTags = $useTags;
    }

    public function handlePostCreation(PostCreated $event): void
    {
        $this->flushListCache();
    }

    public function handlePostUpdate(PostUpdated $event): void
    {
        $this->cache->forget("{$this->prefix}find:{$event->id->value()}");
        $this->flushListCache();
    }

    public function handlePostTagsSync(PostTagsSynchronized $event): void
    {
        $this->cache->forget("{$this->prefix}find:{$event->postId}");
        $this->flushListCache();
    }

    public function handlePostDeletion(PostDeleted $event): void
    {
        $this->cache->forget("{$this->prefix}find:{$event->postId->value()}");
        $this->flushListCache();
    }

    public function handlePostsBulkDeletion(PostsBulkDeleted $event): void
    {
        foreach ($event->postIds->all() as $postId) {
            $this->cache->forget("{$this->prefix}find:{$postId->value()}");
        }
        $this->flushListCache();
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(PostCreated::class, [$this, 'handlePostCreation']);
        $events->listen(PostUpdated::class, [$this, 'handlePostUpdate']);
        $events->listen(PostTagsSynchronized::class, [$this, 'handlePostTagsSync']);
        $events->listen(PostDeleted::class, [$this, 'handlePostDeletion']);
        $events->listen(PostsBulkDeleted::class, [$this, 'handlePostsBulkDeletion']);
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
        if (! $this->useTags) {
            return false;
        }

        return $this->cache->getStore() instanceof TaggableStore;
    }
}
