<?php

declare(strict_types=1);

namespace Modules\Tag\Application\Commands;

class CreateTagCommand
{
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
    ) {}
}
