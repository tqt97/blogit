<?php

declare(strict_types=1);

namespace Modules\Categories\Application\Port\ReadModels;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Categories\Application\DTOs\CategoryDTO;
use Modules\Categories\Domain\ValueObjects\Pagination;
use Modules\Categories\Domain\ValueObjects\SearchTerm;
use Modules\Categories\Domain\ValueObjects\Sorting;

interface TagReadModel
{
    /**
     * Paginate tags based on search criteria and sorting.
     */
    public function paginate(?SearchTerm $search, Pagination $pagination, Sorting $sorting): LengthAwarePaginator;

    /**
     * Find a tag DTO by its ID.
     */
    public function find(int $id): ?CategoryDTO;
}
