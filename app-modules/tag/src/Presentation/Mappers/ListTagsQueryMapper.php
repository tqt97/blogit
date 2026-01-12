<?php

declare(strict_types=1);

namespace Modules\Tag\Presentation\Mappers;

use Modules\Tag\Application\Queries\ListTagsQuery;
use Modules\Tag\Domain\ValueObjects\Pagination;
use Modules\Tag\Domain\ValueObjects\SearchTerm;
use Modules\Tag\Domain\ValueObjects\SortDirection;
use Modules\Tag\Domain\ValueObjects\SortField;
use Modules\Tag\Domain\ValueObjects\Sorting;

final class ListTagsQueryMapper
{
    public function __invoke(array $filters): ListTagsQuery
    {
        $search = SearchTerm::fromNullable($filters['search']);
        $pagination = Pagination::fromInts(
            page: $filters['page'],
            perPage: $filters['per_page'],
            defaultPerPage: (int) config('tag.pagination.default_per_page'),
            maxPerPage: (int) config('tag.pagination.max_per_page'),
        );
        $sorting = new Sorting(
            SortField::fromString($filters['sort'], SortField::Id),
            SortDirection::fromString($filters['direction'], SortDirection::Desc),
        );

        return new ListTagsQuery(
            search: $search,
            pagination: $pagination,
            sorting: $sorting,
        );
    }
}
