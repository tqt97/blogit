<?php

declare(strict_types=1);

namespace Modules\Post\Application\CommandHandlers;

use Modules\Post\Application\Commands\DeletePostCommand;
use Modules\Post\Application\Ports\EventBus\EventBus;
use Modules\Post\Application\Ports\Transaction\TransactionManager;
use Modules\Post\Domain\Events\PostDeleted;
use Modules\Post\Domain\Repositories\PostRepository;
use Modules\Post\Domain\ValueObjects\PostId;

final class DeletePostHandler
{
    public function __construct(
        private readonly PostRepository $repository,
        private readonly TransactionManager $transactionManager,
        private readonly EventBus $eventBus,
    ) {}

    public function handle(DeletePostCommand $command): void
    {
        $this->transactionManager->withinTransaction(function () use ($command) {
            $id = new PostId($command->id);

            $this->repository->delete($id);

            $this->eventBus->publish([new PostDeleted($id)]);
        });
    }
}
