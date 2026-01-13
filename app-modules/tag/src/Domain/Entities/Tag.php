<?php

declare(strict_types=1);

namespace Modules\Tag\Domain\Entities;

use Modules\Tag\Domain\Events\TagCreated;
use Modules\Tag\Domain\Events\TagUpdated;
use Modules\Tag\Domain\ValueObjects\TagId;
use Modules\Tag\Domain\ValueObjects\TagName;
use Modules\Tag\Domain\ValueObjects\TagSlug;

final class Tag
{
    /** @var list<object> */
    private array $events = [];

    private function __construct(
        private readonly ?TagId $id,
        private TagName $name,
        private TagSlug $slug,
    ) {}

    public static function create(TagName $name, TagSlug $slug): self
    {
        return new self(null, $name, $slug);
    }

    public static function reconstitute(TagId $id, TagName $name, TagSlug $slug): self
    {
        return new self($id, $name, $slug);
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

    public function withId(TagId $id): self
    {
        $tag = new self($id, $this->name, $this->slug);

        foreach ($this->events as $event) {
            $tag->record($event);
        }

        if ($this->id === null) {
            $tag->record(new TagCreated($id, $this->name, $this->slug));
        }

        return $tag;
    }

    public function update(TagName $name, TagSlug $slug): void
    {
        if ($this->name->value() === $name->value() && $this->slug->value() === $slug->value()) {
            return;
        }

        $this->name = $name;
        $this->slug = $slug;

        if ($this->id) {
            $this->record(new TagUpdated($this->id, $this->name, $this->slug));
        }
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
