<?php

declare(strict_types=1);

namespace Modules\Categories\Application\DTOs;

use Modules\Categories\Domain\Entities\CategoryEntity;

class CategoryDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?int $parent_id,
        public readonly ?string $description,
        public readonly bool $is_active,
        public readonly ?string $created_at,
        public readonly ?array $children_recursive = null
    ) {}

    public static function fromEntity(CategoryEntity $category): self
    {
        return new self(
            id: $category->id()?->value(),
            name: $category->name()->value(),
            slug: $category->slug()->value(),
            parent_id: $category->parentId()?->value(),
            description: $category->description()->value(),
            is_active: $category->status()->value(),
            created_at: $category->createdAt(),
        );
    }
}
