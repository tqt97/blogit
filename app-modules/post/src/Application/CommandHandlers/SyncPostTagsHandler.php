<?php

declare(strict_types=1);

namespace Modules\Post\Application\CommandHandlers;

use Modules\Post\Application\Commands\SyncPostTagsCommand;
use Modules\Post\Application\Ports\EventBus\EventBus;
use Modules\Post\Application\Ports\Transaction\TransactionManager;
use Modules\Post\Domain\Events\PostTagsSynchronized;
use Modules\Post\Domain\Repositories\PostRepository;
use Modules\Post\Domain\ValueObjects\PostId;
use Modules\Post\Domain\ValueObjects\PostTagIds;

final class SyncPostTagsHandler
{
    public function __construct(
        private readonly PostRepository $repository,
        private readonly TransactionManager $transactionManager,
        private readonly EventBus $eventBus,
    ) {}

    public function handle(SyncPostTagsCommand $command): void
    {
        $this->transactionManager->withinTransaction(function () use ($command) {
            $changed = $this->repository->syncTags(
                new PostId($command->id),
                new PostTagIds($command->tagIds)
            );

            if ($changed) {
                $this->eventBus->publish([
                    new PostTagsSynchronized($command->id, $command->tagIds),
                ]);
            }
        });
    }
}
