<?php declare(strict_types=1);

namespace Modules\Tag\Domain\Entities;

use Modules\Tag\Domain\ValueObjects\TagId;
use Modules\Tag\Domain\ValueObjects\TagName;
use Modules\Tag\Domain\ValueObjects\TagSlug;

final class Tag
{
    public function __construct(
        private ?TagId $id,
        private TagName $name,
        private TagSlug $slug,
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

    public function rename(TagName $name, TagSlug $slug): void
    {
        $this->name = $name;
        $this->slug = $slug;
    }

    /**
     * Infrastructure (mapper) có thể set id sau khi persist
     */
    public function setId(TagId $id): void
    {
        $this->id = $id;
    }
}
