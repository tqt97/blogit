<?php

declare(strict_types=1);

namespace Modules\Category\Domain\Rules;

use DomainException;
use Modules\Categories\Domain\ValueObjects\CategoryId;
use Modules\Categories\Domain\ValueObjects\CategorySlug;
use Modules\Categories\Infrastructure\Repositories\EloquentCategoryRepository;

final class UniqueCategorySlugRule
{
    public function __construct(
        private readonly EloquentCategoryRepository $repo
    ) {}

    public function ensureUnique(CategorySlug $slug, ?CategoryId $ignoreId = null): void
    {
        if ($this->repo->existsBySlug($slug, $ignoreId)) {
            throw new DomainException('Category slug already exists.');
        }
    }
}