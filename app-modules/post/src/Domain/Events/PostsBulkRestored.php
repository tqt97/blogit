<?php

declare(strict_types=1);

namespace Modules\Post\Domain\Events;

use Modules\Post\Domain\ValueObjects\PostIds;

final readonly class PostsBulkRestored
{
    public function __construct(public PostIds $postIds) {}
}
