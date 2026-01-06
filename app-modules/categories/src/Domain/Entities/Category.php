<?php

declare(strict_types=1);

namespace Modules\Categories\Domain\Entities;

use LogicException;
use Modules\Categories\Domain\ValueObjects\CategoryDescription;
use Modules\Categories\Domain\ValueObjects\CategoryId;
use Modules\Categories\Domain\ValueObjects\CategoryIsActive;
use Modules\Categories\Domain\ValueObjects\CategoryName;
use Modules\Categories\Domain\ValueObjects\CategoryParentId;
use Modules\Categories\Domain\ValueObjects\CategorySlug;

final class Category
{
    public function __construct(
        private ?CategoryId $id,
        private CategoryName $name,
        private CategorySlug $slug,
        private CategoryDescription $description,
        private ?CategoryParentId $parentId,
        private CategoryIsActive $isActive,
    ) {}

    public static function create(CategoryName $name, CategorySlug $slug, CategoryDescription $description, CategoryParentId $parentId, CategoryIsActive $isActive): self
    {
        return new self(null, $name, $slug, $description, $parentId, $isActive);
    }

    public function id(): ?CategoryId
    {
        return $this->id
            ?? throw new LogicException('Tag must have an ID');
    }

    public function name(): CategoryName
    {
        return $this->name;
    }

    public function slug(): CategorySlug
    {
        return $this->slug;
    }

    public function rename(CategoryName $name, CategorySlug $slug): void
    {
        $this->name = $name;
        $this->slug = $slug;
    }

    public function description(): CategoryDescription
    {
        return $this->description;
    }

    public function activate(): void
    {
        $this->isActive = CategoryIsActive::active();
    }

    public function deactivate(): void
    {
        $this->isActive = CategoryIsActive::inactive();
    }

    public function status(): CategoryIsActive
    {
        return $this->isActive;
    }

    public function parentId(): ?CategoryParentId
    {
        return $this->parentId;
    }

    /**
     * Mapping function to set the ID after creation
     */
    public function setId(CategoryId $id): void
    {
        if ($this->id !== null) {
            throw new LogicException('Category ID is already set.');
        }

        $this->id = $id;
    }
}
