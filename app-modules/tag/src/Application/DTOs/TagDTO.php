<?php

declare(strict_types=1);

namespace Modules\Tag\Application\DTOs;

use Modules\Tag\Infrastructure\Persistence\Eloquent\Models\TagModel;

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

    public static function fromModel(TagModel $model): self
    {
        return new self(
            id: (int) $model->id,
            name: (string) $model->name,
            slug: (string) $model->slug,
            created_at: $model->created_at?->toISOString() ?? '',
            updated_at: $model->updated_at?->toISOString() ?? '',
        );
    }
}
