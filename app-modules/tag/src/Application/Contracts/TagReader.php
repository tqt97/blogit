<?php

declare(strict_types=1);

namespace Modules\Tag\Application\Contracts;

interface TagReader
{
    public function filterExistingIds(array $ids): array;

    public function listForSelect(): array;
}
