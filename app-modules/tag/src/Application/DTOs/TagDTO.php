<?php

declare(strict_types=1);

namespace Modules\Tag\Application\DTOs;

final readonly class TagDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public string $created_at,
        public string $updated_at,
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
