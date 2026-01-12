<?php

declare(strict_types=1);

namespace Modules\Tag\Application\Commands;

final readonly class UpdateTagCommand
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
    ) {}
}
