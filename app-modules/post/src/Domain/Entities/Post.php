<?php

declare(strict_types=1);

namespace Modules\Post\Domain\Entities;

use Modules\Post\Domain\Events\PostCreated;
use Modules\Post\Domain\Events\PostUpdated;
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

final class Post
{
    /** @var list<object> */
    private array $events = [];

    private function __construct(
        private readonly ?PostId $id,
        private PostUserId $userId,
        private PostCategoryId $categoryId,
        private PostTitle $title,
        private PostSlug $slug,
        private PostExcerpt $excerpt,
        private PostContent $content,
        private PostStatus $status,
        private PostViewCount $viewCount,
        private PostCommentCount $commentCount,
        private PostLikeCount $likeCount,
        private PostPublishedAt $publishedAt,
    ) {}

    public static function create(
        PostUserId $userId,
        PostCategoryId $categoryId,
        PostTitle $title,
        PostSlug $slug,
        PostExcerpt $excerpt,
        PostContent $content,
        PostStatus $status,
        PostViewCount $viewCount,
        PostCommentCount $commentCount,
        PostLikeCount $likeCount,
        PostPublishedAt $publishedAt,
    ): self {
        return new self(null, $userId, $categoryId, $title, $slug, $excerpt, $content, $status, $viewCount, $commentCount, $likeCount, $publishedAt);
    }

    public static function reconstitute(
        PostId $id,
        PostUserId $userId,
        PostCategoryId $categoryId,
        PostTitle $title,
        PostSlug $slug,
        PostExcerpt $excerpt,
        PostContent $content,
        PostStatus $status,
        PostViewCount $viewCount,
        PostCommentCount $commentCount,
        PostLikeCount $likeCount,
        PostPublishedAt $publishedAt,
    ): self {
        return new self($id, $userId, $categoryId, $title, $slug, $excerpt, $content, $status, $viewCount, $commentCount, $likeCount, $publishedAt);
    }

    public function id(): ?PostId
    {
        return $this->id;
    }

    public function title(): PostTitle
    {
        return $this->title;
    }

    public function slug(): PostSlug
    {
        return $this->slug;
    }

    public function excerpt(): PostExcerpt
    {
        return $this->excerpt;
    }

    public function content(): PostContent
    {
        return $this->content;
    }

    public function status(): PostStatus
    {
        return $this->status;
    }

    public function viewCount(): PostViewCount
    {
        return $this->viewCount;
    }

    public function commentCount(): PostCommentCount
    {
        return $this->commentCount;
    }

    public function likeCount(): PostLikeCount
    {
        return $this->likeCount;
    }

    public function publishedAt(): PostPublishedAt
    {
        return $this->publishedAt;
    }

    public function userId(): PostUserId
    {
        return $this->userId;
    }

    public function categoryId(): PostCategoryId
    {
        return $this->categoryId;
    }

    public function withId(PostId $id): self
    {
        $post = new self(
            $id,
            $this->userId,
            $this->categoryId,
            $this->title,
            $this->slug,
            $this->excerpt,
            $this->content,
            $this->status,
            $this->viewCount,
            $this->commentCount,
            $this->likeCount,
            $this->publishedAt,
        );

        foreach ($this->events as $event) {
            $post->record($event);
        }

        if ($this->id === null) {
            $post->record(new PostCreated($id, $this->title, $this->slug));
        }

        return $post;
    }

    public function update(
        PostUserId $userId,
        PostCategoryId $categoryId,
        PostTitle $title,
        PostSlug $slug,
        PostExcerpt $excerpt,
        PostContent $content,
        PostStatus $status,
        PostPublishedAt $publishedAt,
    ): void {
        $this->title = $title;
        $this->slug = $slug;
        $this->excerpt = $excerpt;
        $this->content = $content;
        $this->status = $status;
        $this->publishedAt = $publishedAt;
        $this->categoryId = $categoryId;
        $this->userId = $userId;

        if ($this->id) {
            $this->record(new PostUpdated($this->id, $this->title, $this->slug));
        }
    }

    public function publish(PostPublishedAt $publishedAt): void
    {
        $this->status = PostStatus::Published;
        $this->publishedAt = $publishedAt;

        if ($this->id) {
            $this->record(new PostUpdated($this->id, $this->title, $this->slug));
        }
    }

    public function pullEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }

    private function record(object $event): void
    {
        $this->events[] = $event;
    }
}
