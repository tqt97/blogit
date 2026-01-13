<?php

declare(strict_types=1);

namespace Modules\Post\Domain\Events;

use Modules\Post\Domain\ValueObjects\PostId;
use Modules\Post\Domain\ValueObjects\PostPublishedAt;

final readonly class PostPublished
{
    public function __construct(public PostId $id, public PostPublishedAt $publishedAt) {}
}
