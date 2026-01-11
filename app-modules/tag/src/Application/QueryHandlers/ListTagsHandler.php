<?php

declare(strict_types=1);

namespace Modules\Tag\Application\QueryHandlers;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Tag\Application\Ports\ReadModels\TagReadModel;
use Modules\Tag\Application\Queries\ListTagsQuery;

final class ListTagsHandler
{
    public function __construct(private TagReadModel $reader) {}

    public function handle(ListTagsQuery $query): LengthAwarePaginator
    {
        return $this->reader->paginate($query->search, $query->pagination, $query->sorting);
    }
}
