<?php

declare(strict_types=1);

namespace Modules\Tag\Application\Ports\ReadModels;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Tag\Application\DTOs\TagDTO;
use Modules\Tag\Domain\ValueObjects\Pagination;
use Modules\Tag\Domain\ValueObjects\SearchTerm;
use Modules\Tag\Domain\ValueObjects\Sorting;

interface TagReadModel
{
    /**
     * Paginate tags based on search criteria and sorting.
     */
    public function paginate(?SearchTerm $search, Pagination $pagination, Sorting $sorting): LengthAwarePaginator;

    /**
     * Find a tag DTO by its ID.
     */
    public function find(int $id): ?TagDTO;

    /**
     * Find multiple tag DTOs by their IDs.
     *
     * @param  int[]  $ids
     * @return TagDTO[]
     */
    public function getByIds(array $ids): array;
}
