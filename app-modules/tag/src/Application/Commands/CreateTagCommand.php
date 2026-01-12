<?php

declare(strict_types=1);

namespace Modules\Tag\Application\Commands;

final readonly class CreateTagCommand
{
    public function __construct(
        public string $name,
        public string $slug,
    ) {}
}
