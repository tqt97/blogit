<?php

namespace Modules\Tag\Domain\Repositories;

use Modules\Tag\Domain\Entities\Tag;
use Modules\Tag\Domain\ValueObjects\TagId;
use Modules\Tag\Domain\ValueObjects\TagSlug;

interface TagRepository
{
    public function getById(TagId $id): ?Tag;

    public function existsBySlug(TagSlug $slug, ?TagId $ignoreId = null): bool;

    public function save(Tag $tag): Tag;

    public function delete(TagId $id): void;

    public function deleteMany(array $ids): void;
}
