<?php

declare(strict_types=1);

namespace Modules\Post\Domain\Events;

use Modules\Post\Domain\ValueObjects\PostId;

final readonly class PostForceDeleted
{
    public function __construct(public PostId $postId) {}
}
