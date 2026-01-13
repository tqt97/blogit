<?php

declare(strict_types=1);

namespace Modules\Post\Application\Commands;

final readonly class IncrementPostViewsCountCommand
{
    public function __construct(public int $id) {}
}
