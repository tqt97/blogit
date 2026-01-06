<?php

declare(strict_types=1);

namespace Modules\Tag\Application\DTOs;

class TagDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly string $created_at,
        public readonly string $updated_at,
    ) {}

    public static function fromArray(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            name: (string) $row['name'],
            slug: (string) $row['slug'],
            created_at: (string) $row['created_at'],
            updated_at: (string) $row['updated_at'],
        );
    }
}
