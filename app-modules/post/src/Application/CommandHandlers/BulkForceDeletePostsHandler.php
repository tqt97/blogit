<?php

declare(strict_types=1);

namespace Modules\Post\Application\CommandHandlers;

use Modules\Post\Application\Commands\BulkForceDeletePostsCommand;
use Modules\Post\Application\Ports\EventBus\EventBus;
use Modules\Post\Application\Ports\Transaction\TransactionManager;
use Modules\Post\Domain\Events\PostsBulkForceDeleted;
use Modules\Post\Domain\Repositories\PostRepository;

final class BulkForceDeletePostsHandler
{
    public function __construct(
        private readonly PostRepository $repository,
        private readonly TransactionManager $transactionManager,
        private readonly EventBus $eventBus,
    ) {}

    public function handle(BulkForceDeletePostsCommand $command): void
    {
        $this->transactionManager->withinTransaction(function () use ($command) {
            $this->repository->forceDeleteMany($command->ids);

            $this->eventBus->publish([new PostsBulkForceDeleted($command->ids)]);
        });
    }
}
