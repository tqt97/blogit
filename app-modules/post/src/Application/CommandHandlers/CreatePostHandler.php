<?php

declare(strict_types=1);

namespace Modules\Post\Application\CommandHandlers;

use Modules\Post\Application\Commands\CreatePostCommand;
use Modules\Post\Application\Ports\EventBus\EventBus;
use Modules\Post\Application\Ports\Transaction\TransactionManager;
use Modules\Post\Application\Results\CreatePostResult;
use Modules\Post\Domain\Entities\Post;
use Modules\Post\Domain\Events\PostTagsSynchronized;
use Modules\Post\Domain\Repositories\PostRepository;
use Modules\Post\Domain\ValueObjects\PostCategoryId;
use Modules\Post\Domain\ValueObjects\PostCommentCount;
use Modules\Post\Domain\ValueObjects\PostContent;
use Modules\Post\Domain\ValueObjects\PostExcerpt;
use Modules\Post\Domain\ValueObjects\PostLikeCount;
use Modules\Post\Domain\ValueObjects\PostPublishedAt;
use Modules\Post\Domain\ValueObjects\PostSlug;
use Modules\Post\Domain\ValueObjects\PostStatus;
use Modules\Post\Domain\ValueObjects\PostTagIds;
use Modules\Post\Domain\ValueObjects\PostTitle;
use Modules\Post\Domain\ValueObjects\PostUserId;
use Modules\Post\Domain\ValueObjects\PostViewCount;

final class CreatePostHandler
{
    public function __construct(
        private readonly PostRepository $repository,
        private readonly TransactionManager $transactionManager,
        private readonly EventBus $eventBus,
    ) {}

    public function handle(CreatePostCommand $command): CreatePostResult
    {
        return $this->transactionManager->withinTransaction(function () use ($command) {
            $data = Post::create(
                new PostUserId($command->userId),
                $command->categoryId ? new PostCategoryId($command->categoryId) : null,
                new PostTitle($command->title),
                new PostSlug($command->slug),
                $command->excerpt ? new PostExcerpt($command->excerpt) : null,
                new PostContent($command->content),
                PostStatus::fromString($command->status),
                new PostViewCount($command->viewCount),
                new PostCommentCount($command->commentCount),
                new PostLikeCount($command->likeCount),
                new PostPublishedAt($command->publishedAt),
            );

            $post = $this->repository->save($data);

            if (! empty($command->tagIds)) {
                $this->repository->syncTags($post->id(), new PostTagIds($command->tagIds));
                $this->eventBus->publish([
                    new PostTagsSynchronized($post->id()->value(), $command->tagIds),
                ]);
            }

            $this->eventBus->publish($post->pullEvents());

            return new CreatePostResult($post->id()->value());
        });
    }
}
