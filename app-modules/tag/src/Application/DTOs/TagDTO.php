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
}
