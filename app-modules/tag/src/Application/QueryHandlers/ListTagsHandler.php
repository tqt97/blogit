<?php

declare(strict_types=1);

namespace Modules\Tag\Application\QueryHandlers;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Tag\Application\Contracts\TagReader;
use Modules\Tag\Application\Queries\ListTagsQuery;

final class ListTagsHandler
{
    public function __construct(private TagReader $reader) {}

    public function handle(ListTagsQuery $q): LengthAwarePaginator
    {
        return $this->reader->paginate(
            search: $q->search,
            page: $q->page,
            perPage: $q->perPage,
            sort: $q->sort,
            direction: $q->direction
        );
    }
}
