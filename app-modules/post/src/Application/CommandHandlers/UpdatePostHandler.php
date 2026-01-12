<?php

declare(strict_types=1);

namespace Modules\Post\Application\CommandHandlers;

use Modules\Post\Application\Commands\UpdatePostCommand;
use Modules\Post\Application\Ports\EventBus\EventBus;
use Modules\Post\Application\Ports\Transaction\TransactionManager;
use Modules\Post\Application\Results\UpdatePostResult;
use Modules\Post\Domain\Events\PostTagsSynchronized;
use Modules\Post\Domain\Exceptions\PostNotFoundException;
use Modules\Post\Domain\Repositories\PostRepository;
use Modules\Post\Domain\ValueObjects\PostCategoryId;
use Modules\Post\Domain\ValueObjects\PostContent;
use Modules\Post\Domain\ValueObjects\PostExcerpt;
use Modules\Post\Domain\ValueObjects\PostId;
use Modules\Post\Domain\ValueObjects\PostPublishedAt;
use Modules\Post\Domain\ValueObjects\PostSlug;
use Modules\Post\Domain\ValueObjects\PostStatus;
use Modules\Post\Domain\ValueObjects\PostTagIds;
use Modules\Post\Domain\ValueObjects\PostTitle;
use Modules\Post\Domain\ValueObjects\PostUserId;

final class UpdatePostHandler
{
    public function __construct(
        private readonly PostRepository $repository,
        private readonly TransactionManager $transactionManager,
        private readonly EventBus $eventBus,
    ) {}

    public function handle(UpdatePostCommand $command): UpdatePostResult
    {
        return $this->transactionManager->withinTransaction(function () use ($command) {
            $post = $this->repository->find(new PostId($command->id));

            if (! $post) {
                throw new PostNotFoundException;
            }

            $post->update(
                new PostUserId($command->userId),
                new PostCategoryId($command->categoryId),
                new PostTitle($command->title),
                new PostSlug($command->slug),
                new PostExcerpt($command->excerpt),
                new PostContent($command->content),
                PostStatus::fromString($command->status),
                new PostPublishedAt($command->publishedAt),
            );

            $post = $this->repository->save($post);

            if (! empty($command->tagIds)) {
                $tagsChanged = $this->repository->syncTags($post->id(), new PostTagIds($command->tagIds));
                if ($tagsChanged) {
                    $this->eventBus->publish([
                        new PostTagsSynchronized($post->id()->value(), $command->tagIds),
                    ]);
                }
            }

            $this->eventBus->publish($post->pullEvents());

            return new UpdatePostResult(new PostId($post->id()->value()));
        });
    }
}
