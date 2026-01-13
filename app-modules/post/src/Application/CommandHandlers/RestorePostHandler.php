<?php

declare(strict_types=1);

namespace Modules\Post\Application\CommandHandlers;

use Modules\Post\Application\Commands\RestorePostCommand;
use Modules\Post\Application\Ports\EventBus\EventBus;
use Modules\Post\Application\Ports\Transaction\TransactionManager;
use Modules\Post\Domain\Events\PostRestored;
use Modules\Post\Domain\Repositories\PostRepository;
use Modules\Post\Domain\ValueObjects\PostId;

final class RestorePostHandler
{
    public function __construct(
        private readonly PostRepository $repository,
        private readonly TransactionManager $transactionManager,
        private readonly EventBus $eventBus,
    ) {}

    public function handle(RestorePostCommand $command): void
    {
        $this->transactionManager->withinTransaction(function () use ($command) {
            $id = new PostId($command->id);

            $this->repository->restore($id);

            $this->eventBus->publish([new PostRestored($id)]);
        });
    }
}
