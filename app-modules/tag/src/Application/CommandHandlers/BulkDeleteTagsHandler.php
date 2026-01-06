<?php

namespace Modules\Tag\Application\CommandHandlers;

use Modules\Tag\Application\Commands\BulkDeleteTagsCommand;
use Modules\Tag\Domain\Repositories\TagRepository;
use Modules\Tag\Domain\ValueObjects\TagId;

final class BulkDeleteTagsHandler
{
    public function __construct(private readonly TagRepository $repo) {}

    public function handle(BulkDeleteTagsCommand $cmd): void
    {
        $ids = array_values(array_unique(array_map('intval', $cmd->ids)));
        $ids = array_filter($ids, fn ($v) => $v > 0);

        $this->repo->deleteMany(array_map(fn (int $id): TagId => new TagId($id), $ids));
    }
}
