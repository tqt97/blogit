<?php

declare(strict_types=1);

namespace Modules\Post\Presentation\Mappers;

use Modules\Post\Application\Commands\UpdatePostCommand;

final class UpdatePostCommandMapper
{
    public function __invoke(int $id, array $data): UpdatePostCommand
    {
        return new UpdatePostCommand(
            id: $id,
            userId: (int) $data['user_id'],
            categoryId: isset($data['category_id']) ? (int) $data['category_id'] : null,
            title: (string) $data['title'],
            slug: (string) $data['slug'],
            excerpt: isset($data['excerpt']) ? (string) $data['excerpt'] : null,
            content: (string) $data['content'],
            status: (string) $data['status'],
            publishedAt: isset($data['published_at']) ? (string) $data['published_at'] : null,
            tagIds: array_key_exists('tags', $data) ? $data['tags'] : null
        );
    }
}
