<?php

declare(strict_types=1);

namespace Modules\Tag\Application\CommandHandlers;

use Modules\Tag\Application\Commands\BulkDeleteTagsCommand;
use Modules\Tag\Application\Ports\EventBus\EventBus;
use Modules\Tag\Application\Ports\Transaction\TransactionManager;
use Modules\Tag\Domain\Events\TagsBulkDeleted;
use Modules\Tag\Domain\Repositories\TagRepository;

final class BulkDeleteTagsHandler
{
    public function __construct(
        private readonly TagRepository $repository,
        private readonly TransactionManager $transactionManager,
        private readonly EventBus $eventBus,
    ) {}

    public function handle(BulkDeleteTagsCommand $command): void
    {
        $this->transactionManager->withinTransaction(function () use ($command) {
            $this->repository->deleteMany($command->ids);

            $this->eventBus->publish([new TagsBulkDeleted($command->ids)]);
        });
    }
}
