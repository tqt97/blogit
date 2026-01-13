<?php

declare(strict_types=1);

namespace Modules\Post\Application\QueryHandlers;

use Modules\Post\Application\DTOs\PostDTO;
use Modules\Post\Application\Ports\ReadModels\PostReadModel;
use Modules\Post\Application\Queries\GetRelatedPostsQuery;

final class GetRelatedPostsHandler
{
    public function __construct(private readonly PostReadModel $reader) {}

    /** @return PostDTO[] */
    public function handle(GetRelatedPostsQuery $query): array
    {
        return $this->reader->getRelated($query->postId, $query->limit);
    }
}
