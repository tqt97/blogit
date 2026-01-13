<?php

declare(strict_types=1);

namespace Modules\Categories\Application\Commands;

final readonly class CreateCategoryCommand
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?int $parent_id = null,
        public ?string $description = null,
        public bool $is_active = true,
    ) {}
}
