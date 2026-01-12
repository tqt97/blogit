<?php

declare(strict_types=1);

namespace Modules\Tag\Application\CommandHandlers;

use Modules\Tag\Application\Commands\DeleteTagCommand;
use Modules\Tag\Application\Ports\EventBus\EventBus;
use Modules\Tag\Application\Ports\Transaction\TransactionManager;
use Modules\Tag\Domain\Events\TagDeleted;
use Modules\Tag\Domain\Repositories\TagRepository;
use Modules\Tag\Domain\ValueObjects\TagId;

final class DeleteTagHandler
{
    public function __construct(
        private readonly TagRepository $repository,
        private readonly TransactionManager $transactionManager,
        private readonly EventBus $eventBus,
    ) {}

    public function handle(DeleteTagCommand $command): void
    {
        $this->transactionManager->withinTransaction(function () use ($command) {
            $id = new TagId($command->id);

            $this->repository->delete($id);

            $this->eventBus->publish([new TagDeleted($id)]);
        });
    }
}
