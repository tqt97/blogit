<?php

declare(strict_types=1);

namespace Modules\Category\Domain\Events;

use Modules\Categories\Domain\ValueObjects\CategoryDescription;
use Modules\Categories\Domain\ValueObjects\CategoryId;
use Modules\Categories\Domain\ValueObjects\CategoryIsActive;
use Modules\Categories\Domain\ValueObjects\CategoryName;
use Modules\Categories\Domain\ValueObjects\CategoryParentId;
use Modules\Categories\Domain\ValueObjects\CategorySlug;

final readonly class CategoryCreated
{
    public function __construct(
        public ?CategoryId $id,
        public CategoryName $name,
        public CategorySlug $slug,
        public CategoryDescription $description,
        public ?CategoryParentId $parentId,
        public CategoryIsActive $isActive,
    ) {}
}
