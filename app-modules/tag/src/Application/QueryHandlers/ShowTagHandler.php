<?php

declare(strict_types=1);

namespace Modules\Tag\Application\QueryHandlers;

use Modules\Tag\Application\DTOs\TagDTO;
use Modules\Tag\Application\Ports\ReadModels\TagReadModel;
use Modules\Tag\Application\Queries\ShowTagQuery;
use Modules\Tag\Domain\Exceptions\TagNotFoundException;

final class ShowTagHandler
{
    public function __construct(private readonly TagReadModel $reader) {}

    public function handle(ShowTagQuery $query): TagDTO
    {
        $tag = $this->reader->find($query->id);

        if (! $tag) {
            throw new TagNotFoundException;
        }

        return $tag;
    }
}
