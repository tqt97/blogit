<?php

declare(strict_types=1);

namespace Modules\Post\Application\QueryHandlers;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Post\Application\Ports\ReadModels\PostReadModel;
use Modules\Post\Application\Queries\ListPostsQuery;

final class ListPostsHandler
{
    public function __construct(private PostReadModel $reader) {}

    public function handle(ListPostsQuery $query): LengthAwarePaginator
    {
        return $this->reader->paginate($query->search, $query->pagination, $query->sorting);
    }
}
