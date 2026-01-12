<?php

declare(strict_types=1);

namespace Modules\Tag\Application\CommandHandlers;

use Modules\Tag\Application\Commands\CreateTagCommand;
use Modules\Tag\Application\Ports\EventBus\EventBus;
use Modules\Tag\Application\Ports\Transaction\TransactionManager;
use Modules\Tag\Application\Results\CreateTagResult;
use Modules\Tag\Domain\Entities\Tag;
use Modules\Tag\Domain\Repositories\TagRepository;
use Modules\Tag\Domain\ValueObjects\TagId;
use Modules\Tag\Domain\ValueObjects\TagName;
use Modules\Tag\Domain\ValueObjects\TagSlug;

final class CreateTagHandler
{
    public function __construct(
        private readonly TagRepository $repository,
        private readonly TransactionManager $transactionManager,
        private readonly EventBus $eventBus,
    ) {}

    public function handle(CreateTagCommand $command): CreateTagResult
    {
        return $this->transactionManager->withinTransaction(function () use ($command) {
            $data = Tag::create(new TagName($command->name), new TagSlug($command->slug));

            $tag = $this->repository->save($data);

            $this->eventBus->publish($tag->pullEvents());

            return new CreateTagResult(new TagId($tag->id()->value()));
        });
    }
}
