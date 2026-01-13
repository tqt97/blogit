<?php

declare(strict_types=1);

namespace Modules\Post\Application\QueryHandlers;

use Modules\Post\Application\DTOs\PostDTO;
use Modules\Post\Application\Ports\ReadModels\PostReadModel;
use Modules\Post\Application\Queries\FindPostBySlugQuery;
use Modules\Post\Domain\Exceptions\PostNotFoundException;

final class FindPostBySlugHandler
{
    public function __construct(private readonly PostReadModel $reader) {}

    public function handle(FindPostBySlugQuery $query): PostDTO
    {
        $post = $this->reader->findBySlug($query->slug);

        if (! $post) {
            throw new PostNotFoundException;
        }

        return $post;
    }
}
