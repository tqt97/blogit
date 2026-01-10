<?php

declare(strict_types=1);

namespace Modules\Tag\Application\CommandHandlers;

use Modules\Tag\Application\Commands\BulkDeleteTagsCommand;
use Modules\Tag\Domain\Repositories\TagRepository;

final class BulkDeleteTagsHandler
{
    public function __construct(private readonly TagRepository $repository) {}

    public function handle(BulkDeleteTagsCommand $command): void
    {
        $this->repository->deleteMany($command->ids->all());
    }
}
