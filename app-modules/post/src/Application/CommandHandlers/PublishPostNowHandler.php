<?php

declare(strict_types=1);

namespace Modules\Post\Application\CommandHandlers;

use Modules\Post\Application\Commands\PublishPostNowCommand;
use Modules\Post\Application\Ports\EventBus\EventBus;
use Modules\Post\Application\Ports\Transaction\TransactionManager;
use Modules\Post\Domain\Exceptions\PostNotFoundException;
use Modules\Post\Domain\Repositories\PostRepository;
use Modules\Post\Domain\ValueObjects\PostId;
use Modules\Post\Domain\ValueObjects\PostPublishedAt;

final class PublishPostNowHandler
{
    public function __construct(
        private readonly PostRepository $repository,
        private readonly TransactionManager $tx,
        private readonly EventBus $eventBus
    ) {}

    public function handle(PublishPostNowCommand $command): void
    {
        $this->tx->withinTransaction(function () use ($command) {
            $post = $this->repository->find(new PostId($command->id));

            if (! $post) {
                throw new PostNotFoundException;
            }

            // We need a specific method in Domain Entity to publish, or use update.
            // Since `update` requires all fields, we should ideally have `publish()`.
            // For now, I will add `publish()` to Post entity.
            $post->publish(new PostPublishedAt(now()->toISOString()));

            $this->repository->save($post);
            $this->eventBus->publish($post->pullEvents());
        });
    }
}
