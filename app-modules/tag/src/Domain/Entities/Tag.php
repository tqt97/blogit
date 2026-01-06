<?php

declare(strict_types=1);

namespace Modules\Tag\Domain\Entities;

use Modules\Tag\Domain\ValueObjects\TagCreatedAt;
use Modules\Tag\Domain\ValueObjects\TagId;
use Modules\Tag\Domain\ValueObjects\TagName;
use Modules\Tag\Domain\ValueObjects\TagSlug;
use Modules\Tag\Domain\ValueObjects\TagUpdatedAt;

final class Tag
{
    public function __construct(
        private readonly ?TagId $id,
        private TagName $name,
        private TagSlug $slug,
        private readonly ?TagCreatedAt $createdAt = null,
        private readonly ?TagUpdatedAt $updatedAt = null,
    ) {}

    public static function create(TagName $name, TagSlug $slug): self
    {
        return new self(null, $name, $slug);
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

    public function setId(TagId $id): void
    {
        $this->id = $id;
    }

    public function rename(TagName $name): void
    {
        $this->name = $name;
    }

    public function changeSlug(TagSlug $slug): void
    {
        $this->slug = $slug;
    }
}
