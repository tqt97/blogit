<?php

declare(strict_types=1);

namespace Modules\Post\Application\QueryHandlers;

use Modules\Post\Application\DTOs\PostDTO;
use Modules\Post\Application\Ports\ReadModels\PostReadModel;
use Modules\Post\Application\Queries\ShowPostQuery;
use Modules\Post\Domain\Exceptions\PostNotFoundException;

final class ShowPostHandler
{
    public function __construct(private readonly PostReadModel $reader) {}

    public function handle(ShowPostQuery $query): PostDTO
    {
        $post = $this->reader->find($query->id);

        if (! $post) {
            throw new PostNotFoundException;
        }

        return $post;
    }
}
