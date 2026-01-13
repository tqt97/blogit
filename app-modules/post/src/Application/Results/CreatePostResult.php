<?php

declare(strict_types=1);

namespace Modules\Post\Application\Results;

final readonly class CreatePostResult
{
    public function __construct(
        public int $id,
    ) {}
}
