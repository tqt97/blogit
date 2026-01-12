<?php

declare(strict_types=1);

namespace Modules\Post\Application\CommandHandlers;

use Modules\Post\Application\Commands\BulkDeletePostsCommand;
use Modules\Post\Application\Ports\EventBus\EventBus;
use Modules\Post\Application\Ports\Transaction\TransactionManager;
use Modules\Post\Domain\Events\PostsBulkDeleted;
use Modules\Post\Domain\Repositories\PostRepository;

final class BulkDeletePostsHandler
{
    public function __construct(
        private readonly PostRepository $repository,
        private readonly TransactionManager $transactionManager,
        private readonly EventBus $eventBus,
    ) {}

    public function handle(BulkDeletePostsCommand $command): void
    {
        $this->transactionManager->withinTransaction(function () use ($command) {
            $this->repository->deleteMany($command->ids);

            $this->eventBus->publish([new PostsBulkDeleted($command->ids)]);
        });
    }
}
