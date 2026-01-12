<?php

declare(strict_types=1);

namespace Modules\Post\Presentation\Mappers;

use Modules\Post\Application\Queries\ListPostsQuery;
use Modules\Post\Domain\ValueObjects\Pagination;
use Modules\Post\Domain\ValueObjects\SearchTerm;
use Modules\Post\Domain\ValueObjects\SortDirection;
use Modules\Post\Domain\ValueObjects\SortField;
use Modules\Post\Domain\ValueObjects\Sorting;

final class ListPostsQueryMapper
{
    public function __invoke(array $filters): ListPostsQuery
    {
        $search = SearchTerm::fromNullable($filters['search']);
        $pagination = Pagination::fromInts(
            page: $filters['page'],
            perPage: $filters['per_page'],
            default: (int) config('post.pagination.default_per_page')
        );
        $sorting = new Sorting(
            SortField::fromString($filters['sort'], SortField::Id),
            SortDirection::fromString($filters['direction'], SortDirection::Desc),
        );

        return new ListPostsQuery(
            search: $search,
            pagination: $pagination,
            sorting: $sorting,
        );
    }
}
