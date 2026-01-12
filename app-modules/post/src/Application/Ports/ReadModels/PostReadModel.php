<?php

declare(strict_types=1);

namespace Modules\Post\Application\Ports\ReadModels;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Post\Application\DTOs\PostDTO;
use Modules\Post\Domain\ValueObjects\Pagination;
use Modules\Post\Domain\ValueObjects\SearchTerm;
use Modules\Post\Domain\ValueObjects\Sorting;

interface PostReadModel
{
    /**
     * Paginate posts based on search criteria and sorting.
     */
    public function paginate(?SearchTerm $search, Pagination $pagination, Sorting $sorting): LengthAwarePaginator;

    /**
     * Find a post DTO by its ID.
     */
    public function find(int $id): ?PostDTO;
}
