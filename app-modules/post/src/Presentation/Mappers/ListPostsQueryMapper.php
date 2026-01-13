<?php

declare(strict_types=1);

namespace Modules\Post\Presentation\Mappers;

use Modules\Post\Application\Queries\ListPostsQuery;
use Modules\Post\Domain\ValueObjects\Pagination;
use Modules\Post\Domain\ValueObjects\SearchTerm;
use Modules\Post\Domain\ValueObjects\SortDirection;
use Modules\Post\Domain\ValueObjects\SortField;
use Modules\Post\Domain\ValueObjects\Sorting;
use Modules\Post\Domain\ValueObjects\TrashedFilter;

final class ListPostsQueryMapper
{
    public function __invoke(array $filters): ListPostsQuery
    {
        $search = SearchTerm::fromNullable($filters['search'] ?? null);

        $pagination = Pagination::fromInts(
            page: isset($filters['page']) ? (int) $filters['page'] : 1,
            perPage: isset($filters['per_page']) ? (int) $filters['per_page'] : (int) config('post.pagination.default_per_page'),
            default: (int) config('post.pagination.default_per_page')
        );

        $sorting = new Sorting(
            SortField::fromString($filters['sort'] ?? null, SortField::Id),
            SortDirection::fromString($filters['direction'] ?? null, SortDirection::Desc),
        );

        return new ListPostsQuery(
            search: $search,
            pagination: $pagination,
            sorting: $sorting,
            trashed: TrashedFilter::fromString($filters['trashed'] ?? null),
            categoryId: isset($filters['category_id']) ? (int) $filters['category_id'] : null,
            tagId: isset($filters['tag_id']) ? (int) $filters['tag_id'] : null,
            authorId: isset($filters['author_id']) ? (int) $filters['author_id'] : null,
        );
    }
}
