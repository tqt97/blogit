<?php

declare(strict_types=1);

namespace Modules\Tag\Application\CommandHandlers;

use Modules\Tag\Application\Commands\DeleteTagCommand;
use Modules\Tag\Domain\Repositories\TagRepositoryInterface;
use Modules\Tag\Domain\ValueObjects\TagId;

final class DeleteTagHandler
{
    public function __construct(private readonly TagRepositoryInterface $repo) {}

    public function handle(DeleteTagCommand $cmd): void
    {
        $this->repo->delete(new TagId($cmd->id));
    }
}
