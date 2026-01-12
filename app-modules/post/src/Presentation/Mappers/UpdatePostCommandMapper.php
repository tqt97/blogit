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
            categoryId: (int) $data['category_id'],
            title: (string) $data['title'],
            slug: (string) $data['slug'],
            excerpt: (string) ($data['excerpt'] ?? ''),
            content: (string) $data['content'],
            status: (string) $data['status'],
            publishedAt: isset($data['published_at']) ? (string) $data['published_at'] : null,
            tagIds: $data['tags'] ?? []
        );
    }
}
