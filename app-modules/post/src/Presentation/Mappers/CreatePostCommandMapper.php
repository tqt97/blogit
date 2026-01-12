<?php

declare(strict_types=1);

namespace Modules\Post\Presentation\Mappers;

use Modules\Post\Application\Commands\CreatePostCommand;

final class CreatePostCommandMapper
{
    public function __invoke(array $data): CreatePostCommand
    {
        return new CreatePostCommand(
            userId: (int) $data['user_id'],
            categoryId: (int) $data['category_id'],
            title: (string) $data['title'],
            slug: (string) $data['slug'],
            excerpt: (string) ($data['excerpt'] ?? ''),
            content: (string) $data['content'],
            status: (string) $data['status'],
            viewCount: 0,
            commentCount: 0,
            likeCount: 0,
            publishedAt: isset($data['published_at']) ? (string) $data['published_at'] : null,
            tagIds: $data['tags'] ?? []
        );
    }
}
