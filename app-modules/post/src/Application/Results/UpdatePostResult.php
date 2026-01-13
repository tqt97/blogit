<?php

declare(strict_types=1);

namespace Modules\Post\Application\Results;

final readonly class UpdatePostResult
{
    public function __construct(
        public int $id,
    ) {}
}
