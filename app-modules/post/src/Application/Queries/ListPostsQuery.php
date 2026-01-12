<?php

declare(strict_types=1);

namespace Modules\Post\Application\Queries;

use Modules\Post\Domain\ValueObjects\Pagination;
use Modules\Post\Domain\ValueObjects\SearchTerm;
use Modules\Post\Domain\ValueObjects\Sorting;

final readonly class ListPostsQuery
{
    public function __construct(
        public ?SearchTerm $search,
        public Pagination $pagination,
        public Sorting $sorting,
    ) {}
}
