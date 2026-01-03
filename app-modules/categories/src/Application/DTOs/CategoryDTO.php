<?php

declare(strict_types=1);

namespace Modules\Category\Application\DTOs;

use Modules\Categories\Domain\Entities\Category;

class CategoryDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?int $parent_id,
        public readonly string $description,
        public readonly bool $is_active,
    ) {}


    public static function fromEntity(Category $category): self
    {
        return new self(
            id: $category->id()?->value(),
            name: $category->name()->value(),
            slug: $category->slug()->value() ?? "",
            parent_id: $category->parentId()?->value(),
            description: $category->description()->value() ?? "",
            is_active: $category->status()->value(),
        );
    }
}