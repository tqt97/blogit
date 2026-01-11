<?php

declare(strict_types=1);

namespace Modules\Tag\Application\CommandHandlers;

use Modules\Tag\Application\Commands\UpdateTagCommand;
use Modules\Tag\Application\Ports\EventBus\EventBus;
use Modules\Tag\Application\Ports\Transaction\TransactionManager;
use Modules\Tag\Domain\Exceptions\TagNotFoundException;
use Modules\Tag\Domain\Repositories\TagRepository;
use Modules\Tag\Domain\ValueObjects\TagId;
use Modules\Tag\Domain\ValueObjects\TagName;
use Modules\Tag\Domain\ValueObjects\TagSlug;

final class UpdateTagHandler
{
    public function __construct(
        private readonly TagRepository $repository,
        private readonly TransactionManager $transactionManager,
        private readonly EventBus $eventBus,
    ) {}

    public function handle(UpdateTagCommand $command): void
    {
        $this->transactionManager->withinTransaction(function () use ($command) {
            $tag = $this->repository->find(new TagId($command->id));

            if (! $tag) {
                throw new TagNotFoundException;
            }

            $tag->rename(new TagName($command->name));
            $tag->changeSlug(new TagSlug($command->slug));

            $this->repository->save($tag);

            $this->eventBus->publish($tag->pullEvents());
        });
    }
}
