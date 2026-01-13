<?php

declare(strict_types=1);

namespace Modules\Post\Application\CommandHandlers;

use Modules\Post\Application\Commands\ForceDeletePostCommand;
use Modules\Post\Application\Ports\EventBus\EventBus;
use Modules\Post\Application\Ports\Transaction\TransactionManager;
use Modules\Post\Domain\Events\PostForceDeleted;
use Modules\Post\Domain\Repositories\PostRepository;
use Modules\Post\Domain\ValueObjects\PostId;

final class ForceDeletePostHandler
{
    public function __construct(
        private readonly PostRepository $repository,
        private readonly TransactionManager $transactionManager,
        private readonly EventBus $eventBus,
    ) {}

    public function handle(ForceDeletePostCommand $command): void
    {
        $this->transactionManager->withinTransaction(function () use ($command) {
            $id = new PostId($command->id);

            $this->repository->forceDelete($id);

            $this->eventBus->publish([new PostForceDeleted($id)]);
        });
    }
}
