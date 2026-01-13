<?php

declare(strict_types=1);

namespace Modules\Tag\Application\Queries;

use Modules\Tag\Domain\ValueObjects\Pagination;
use Modules\Tag\Domain\ValueObjects\SearchTerm;
use Modules\Tag\Domain\ValueObjects\Sorting;

final readonly class ListTagsQuery
{
    public function __construct(
        public ?SearchTerm $search,
        public Pagination $pagination,
        public Sorting $sorting,
    ) {}
}
