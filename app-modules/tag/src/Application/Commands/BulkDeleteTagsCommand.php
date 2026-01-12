<?php

declare(strict_types=1);

namespace Modules\Tag\Application\Commands;

use Modules\Tag\Domain\ValueObjects\TagIds;

final readonly class BulkDeleteTagsCommand
{
    public function __construct(public TagIds $ids) {}
}
