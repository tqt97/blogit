<?php

declare(strict_types=1);

namespace Modules\Tag\Application\CommandHandlers;

use Modules\Tag\Application\Commands\DeleteTagCommand;
use Modules\Tag\Domain\Repositories\TagRepository;
use Modules\Tag\Domain\ValueObjects\TagId;

final class DeleteTagHandler
{
    public function __construct(private readonly TagRepository $repository) {}

    public function handle(DeleteTagCommand $command): void
    {
        $this->repository->delete(new TagId($command->id));
    }
}
