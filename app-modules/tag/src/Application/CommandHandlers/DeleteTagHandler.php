<?php

declare(strict_types=1);

namespace Modules\Tag\Application\CommandHandlers;

use Modules\Tag\Application\Commands\DeleteTagCommand;
use Modules\Tag\Domain\Repositories\TagRepository;
use Modules\Tag\Domain\ValueObjects\TagId;

final class DeleteTagHandler
{
    public function __construct(private readonly TagRepository $repo) {}

    public function handle(DeleteTagCommand $cmd): void
    {
        $id = new TagId($cmd->id);
        $tag = $this->repo->getById($id);
        if (! $tag) {
            return;
        }

        $this->repo->delete($id);
    }
}
