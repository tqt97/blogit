<?php

declare(strict_types=1);

namespace Modules\Post\Infrastructure\Persistence\Eloquent\Mappers;

use Illuminate\Support\Carbon;
use Modules\Post\Domain\Entities\Post;
use Modules\Post\Domain\ValueObjects\PostCategoryId;
use Modules\Post\Domain\ValueObjects\PostCommentCount;
use Modules\Post\Domain\ValueObjects\PostContent;
use Modules\Post\Domain\ValueObjects\PostExcerpt;
use Modules\Post\Domain\ValueObjects\PostId;
use Modules\Post\Domain\ValueObjects\PostLikeCount;
use Modules\Post\Domain\ValueObjects\PostPublishedAt;
use Modules\Post\Domain\ValueObjects\PostSlug;
use Modules\Post\Domain\ValueObjects\PostStatus;
use Modules\Post\Domain\ValueObjects\PostTitle;
use Modules\Post\Domain\ValueObjects\PostUserId;
use Modules\Post\Domain\ValueObjects\PostViewCount;
use Modules\Post\Infrastructure\Persistence\Eloquent\Models\PostModel;

final class PostMapper
{
    public function toEntity(PostModel $model): Post
    {
        $status = $model->status instanceof PostStatus
            ? $model->status
            : PostStatus::fromString((string) $model->status);

        return Post::reconstitute(
            id: new PostId((int) $model->id),
            userId: new PostUserId((int) $model->user_id),
            categoryId: new PostCategoryId((int) $model->category_id),
            title: new PostTitle($model->title),
            slug: new PostSlug($model->slug),
            excerpt: new PostExcerpt((string) $model->excerpt),
            content: new PostContent($model->content),
            status: $status,
            viewCount: new PostViewCount((int) $model->views_count),
            commentCount: new PostCommentCount((int) $model->comments_count),
            likeCount: new PostLikeCount((int) $model->likes_count),
            publishedAt: new PostPublishedAt($this->formatDate($model->published_at)),
        );
    }

    public function toPersistence(Post $entity, ?PostModel $model): PostModel
    {
        $model ??= new PostModel;

        $model->user_id = $entity->userId()->value();
        $model->category_id = $entity->categoryId()->value();
        $model->title = $entity->title()->value();
        $model->slug = $entity->slug()->value();
        $model->excerpt = $entity->excerpt()->value();
        $model->content = $entity->content()->value();
        $model->status = $entity->status();
        $model->views_count = $entity->viewCount()->value();
        $model->comments_count = $entity->commentCount()->value();
        $model->likes_count = $entity->likeCount()->value();
        $model->published_at = $entity->publishedAt()->value();

        return $model;
    }

    private function formatDate(mixed $date): ?string
    {
        if ($date instanceof Carbon) {
            return $date->toISOString();
        }

        return $date ? (string) $date : null;
    }
}
