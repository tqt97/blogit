<?php
declare(strict_types=1);

namespace Modules\Tag\Application\Commands;

final class UpdateTagCommand
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $slug = null,
    ) {}
}
