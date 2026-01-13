<?php

declare(strict_types=1);

namespace Modules\Post\Application\CommandHandlers;

use Modules\Post\Application\Commands\BulkRestorePostsCommand;
use Modules\Post\Application\Ports\EventBus\EventBus;
use Modules\Post\Application\Ports\Transaction\TransactionManager;
use Modules\Post\Domain\Events\PostsBulkRestored;
use Modules\Post\Domain\Repositories\PostRepository;

final class BulkRestorePostsHandler
{
    public function __construct(
        private readonly PostRepository $repository,
        private readonly TransactionManager $transactionManager,
        private readonly EventBus $eventBus,
    ) {}

    public function handle(BulkRestorePostsCommand $command): void
    {
        $this->transactionManager->withinTransaction(function () use ($command) {
            $this->repository->restoreMany($command->ids);

            $this->eventBus->publish([new PostsBulkRestored($command->ids)]);
        });
    }
}
