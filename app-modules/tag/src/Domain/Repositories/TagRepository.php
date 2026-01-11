<?php

declare(strict_types=1);

namespace Modules\Tag\Domain\Repositories;

use Modules\Tag\Domain\Entities\Tag;
use Modules\Tag\Domain\ValueObjects\TagId;

interface TagRepository
{
    public function find(TagId $id): ?Tag;

    public function save(Tag $tag): Tag;

    public function delete(TagId $id): void;

    public function deleteMany(array $ids): void;
}
