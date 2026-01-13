<?php

declare(strict_types=1);

namespace Modules\Categories\Domain\Entities;

use LogicException;
use Modules\Categories\Domain\ValueObjects\CategoryCreatedAt;
use Modules\Categories\Domain\ValueObjects\CategoryDescription;
use Modules\Categories\Domain\ValueObjects\CategoryId;
use Modules\Categories\Domain\ValueObjects\CategoryIsActive;
use Modules\Categories\Domain\ValueObjects\CategoryName;
use Modules\Categories\Domain\ValueObjects\CategoryParentId;
use Modules\Categories\Domain\ValueObjects\CategorySlug;
use Modules\Category\Domain\Events\CategoryCreated;

final class CategoryEntity
{
    /** @var list<object> */
    private array $events = [];

    public function __construct(
        private ?CategoryId $id,
        private CategoryName $name,
        private CategorySlug $slug,
        private CategoryDescription $description,
        private ?CategoryParentId $parentId,
        private CategoryIsActive $isActive,
        private ?CategoryCreatedAt $createdAt,
    ) {}

    public static function create(CategoryName $name, CategorySlug $slug, CategoryDescription $description, CategoryParentId $parentId, CategoryIsActive $isActive, ?CategoryCreatedAt $createdAt): self
    {
        return new self(null, $name, $slug, $description, $parentId, $isActive, $createdAt);
    }

    public static function reconstitute(CategoryId $id, CategoryName $name, CategorySlug $slug, CategoryDescription $description, CategoryParentId $parentId, CategoryIsActive $isActive, ?CategoryCreatedAt $createdAt): self
    {
        return new self($id, $name, $slug, $description, $parentId, $isActive, $createdAt);
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

    public function createdAt(): ?CategoryCreatedAt
    {
        return $this->createdAt;
    }

    public function withId(CategoryId $id): self
    {
        $category = new self($id, $this->name, $this->slug, $this->description, $this->parentId, $this->isActive, $this->createdAt);

        foreach ($this->events as $event) {
            $category->record($event);
        }

        if ($this->id === null) {
            $category->record(new CategoryCreated($id, $this->name, $this->slug, $this->description, $this->parentId, $this->isActive));
        }

        return $category;
    }

    /**
     * Pulls all recorded domain events from this entity.
     *
     * Once pulled, the events are removed from the entity.
     */
    public function pullEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }

    /**
     * Records a domain event for later publishing.
     */
    private function record(object $event): void
    {
        $this->events[] = $event;
    }
}
