<?php

declare(strict_types=1);

namespace Modules\Tag\Application\CommandHandlers;

use Modules\Tag\Application\Commands\CreateTagCommand;
use Modules\Tag\Application\Ports\EventBus\EventBus;
use Modules\Tag\Application\Ports\Transaction\TransactionManager;
use Modules\Tag\Domain\Entities\Tag;
use Modules\Tag\Domain\Repositories\TagRepository;
use Modules\Tag\Domain\ValueObjects\TagName;
use Modules\Tag\Domain\ValueObjects\TagSlug;

final class CreateTagHandler
{
    public function __construct(
        private readonly TagRepository $repository,
        private readonly TransactionManager $transactionManager,
        private readonly EventBus $eventBus,
    ) {}

    public function handle(CreateTagCommand $command): void
    {
        $this->transactionManager->withinTransaction(function () use ($command) {
            $tag = Tag::create(new TagName($command->name), new TagSlug($command->slug));

            $this->repository->save($tag);

            $this->eventBus->publish($tag->pullEvents());
        });
    }
}
