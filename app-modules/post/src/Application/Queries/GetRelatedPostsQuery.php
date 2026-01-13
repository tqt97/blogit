<?php

declare(strict_types=1);

namespace Modules\Post\Application\Queries;

final readonly class GetRelatedPostsQuery
{
    public function __construct(public int $postId, public int $limit = 4) {}
}
