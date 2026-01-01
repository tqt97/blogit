<?php

declare(strict_types=1);

namespace Modules\Tag\Application\Commands;

class CreateTagCommand
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $slug = null,
    ) {}

    public static function from(array $data): self
    {
        return new self(
            name: $data['name'],
            slug: $data['slug'],
        );
    }
}
