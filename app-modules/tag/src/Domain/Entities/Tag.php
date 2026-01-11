<?php

declare(strict_types=1);

namespace Modules\Tag\Domain\Entities;

use Modules\Tag\Domain\Events\TagCreated;
use Modules\Tag\Domain\Events\TagUpdated;
use Modules\Tag\Domain\ValueObjects\TagCreatedAt;
use Modules\Tag\Domain\ValueObjects\TagId;
use Modules\Tag\Domain\ValueObjects\TagName;
use Modules\Tag\Domain\ValueObjects\TagSlug;
use Modules\Tag\Domain\ValueObjects\TagUpdatedAt;

final class Tag
{
    /** @var list<object> */
    private array $events = [];

    private function __construct(
        private readonly ?TagId $id,
        private TagName $name,
        private TagSlug $slug,
        private readonly ?TagCreatedAt $createdAt = null,
        private readonly ?TagUpdatedAt $updatedAt = null,
    ) {}

    public static function create(TagName $name, TagSlug $slug): self
    {
        $tag = new self(null, $name, $slug);
        $tag->record(new TagCreated($tag));

        return $tag;
    }

    public static function reconstitute(
        TagId $id,
        TagName $name,
        TagSlug $slug,
        ?TagCreatedAt $createdAt,
        ?TagUpdatedAt $updatedAt
    ): self {
        return new self($id, $name, $slug, $createdAt, $updatedAt);
    }

    public function id(): ?TagId
    {
        return $this->id;
    }

    public function name(): TagName
    {
        return $this->name;
    }

    public function slug(): TagSlug
    {
        return $this->slug;
    }

    public function createdAt(): ?TagCreatedAt
    {
        return $this->createdAt;
    }

    public function updatedAt(): ?TagUpdatedAt
    {
        return $this->updatedAt;
    }

    public function rename(TagName $name): void
    {
        $this->name = $name;
        $this->record(new TagUpdated($this));
    }

    public function changeSlug(TagSlug $slug): void
    {
        $this->slug = $slug;
        $this->record(new TagUpdated($this));
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
