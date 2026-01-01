<?php

namespace Modules\Tag\Application\DTOs;

use Modules\Tag\Domain\Entities\Tag;

class TagDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
    ) {}

    public static function fromEntity(Tag $tag): self
    {
        return new self(
            id: $tag->id()?->value() ?? 0,
            name: $tag->name()->value(),
            slug: $tag->slug()->value(),
        );
    }
}
