<?php

declare(strict_types=1);

namespace Modules\Post\Domain\Repositories;

use Modules\Post\Domain\Entities\Post;
use Modules\Post\Domain\ValueObjects\PostId;
use Modules\Post\Domain\ValueObjects\PostIds;
use Modules\Post\Domain\ValueObjects\PostTagIds;

interface PostRepository
{
    public function save(Post $entity): Post;

    public function find(PostId $id): ?Post;

    public function delete(PostId $id): void;

    public function deleteMany(PostIds $ids): void;

    public function restoreMany(PostIds $ids): void;

    public function forceDeleteMany(PostIds $ids): void;

    public function restore(PostId $id): void;

    public function forceDelete(PostId $id): void;

    public function incrementViews(PostId $id): void;

    public function syncTags(PostId $id, PostTagIds $tags): bool;
}
