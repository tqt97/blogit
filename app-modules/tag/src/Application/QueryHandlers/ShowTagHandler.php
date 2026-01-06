<?php

declare(strict_types=1);

namespace Modules\Tag\Application\QueryHandlers;

use Modules\Tag\Application\Contracts\TagReader;
use Modules\Tag\Application\DTOs\TagDTO;
use Modules\Tag\Application\Queries\ShowTagQuery;

final class ShowTagHandler
{
    public function __construct(private readonly TagReader $reader) {}

    public function handle(ShowTagQuery $query): ?TagDTO
    {
        return $this->reader->find($query->id);
    }
}
