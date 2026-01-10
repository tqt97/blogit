<?php

declare(strict_types=1);

namespace Modules\Tag\Application\QueryHandlers;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Tag\Application\Queries\ListTagsQuery;
use Modules\Tag\Application\QueryContracts\TagQueryRepository;

final class ListTagsHandler
{
    public function __construct(private TagQueryRepository $reader) {}

    public function handle(ListTagsQuery $query): LengthAwarePaginator
    {
        return $this->reader->paginate($query->search, $query->pagination, $query->sorting);
    }
}
