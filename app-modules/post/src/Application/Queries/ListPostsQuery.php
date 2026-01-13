<?php

declare(strict_types=1);

namespace Modules\Post\Application\Queries;

use Modules\Post\Domain\ValueObjects\Pagination;
use Modules\Post\Domain\ValueObjects\SearchTerm;
use Modules\Post\Domain\ValueObjects\Sorting;
use Modules\Post\Domain\ValueObjects\TrashedFilter;

final readonly class ListPostsQuery
{
    public function __construct(
        public ?SearchTerm $search,
        public Pagination $pagination,
        public Sorting $sorting,
        public TrashedFilter $trashed = TrashedFilter::Without,
        public ?int $categoryId = null,
        public ?int $tagId = null,
        public ?int $authorId = null,
    ) {}
}
