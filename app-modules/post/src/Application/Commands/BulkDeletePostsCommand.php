<?php

declare(strict_types=1);

namespace Modules\Post\Application\Commands;

use Modules\Post\Domain\ValueObjects\PostIds;

final readonly class BulkDeletePostsCommand
{
    public function __construct(public PostIds $ids) {}
}
