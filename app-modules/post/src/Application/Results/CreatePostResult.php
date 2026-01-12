<?php

declare(strict_types=1);

namespace Modules\Post\Application\Results;

use Modules\Post\Domain\ValueObjects\PostId;

final readonly class CreatePostResult
{
    public function __construct(
        public PostId $id,
    ) {}
}
