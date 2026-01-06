<?php

declare(strict_types=1);

namespace Modules\Categories\Application\Queries;

final class ListCategoriesQuery
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly int $perPage = 15,
        public readonly string $sort = 'id',
        public readonly string $direction = 'desc',
    ) {}
}