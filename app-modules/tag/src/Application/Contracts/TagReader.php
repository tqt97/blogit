<?php

declare(strict_types=1);

namespace Modules\Tag\Application\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Tag\Application\DTOs\TagDTO;

interface TagReader
{
    public function paginate(?string $search, int $page, int $perPage, string $sort, string $direction): LengthAwarePaginator;

    public function find(int $id): ?TagDTO;
}
