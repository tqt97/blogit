<?php

declare(strict_types=1);

namespace Modules\Tag\Domain\Rules;

use DomainException;
use Modules\Tag\Domain\Repositories\TagRepositoryInterface;
use Modules\Tag\Domain\ValueObjects\TagId;
use Modules\Tag\Domain\ValueObjects\TagSlug;

final class UniqueTagSlugRule
{
    public function __construct(
        private readonly TagRepositoryInterface $repo
    ) {}

    public function ensureUnique(TagSlug $slug, ?TagId $ignoreId = null): void
    {
        if ($this->repo->existsBySlug($slug, $ignoreId)) {
            throw new DomainException('Tag slug already exists.');
        }
    }
}
