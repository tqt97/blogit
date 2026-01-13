<?php

declare(strict_types=1);

namespace Modules\Post\Application\Commands;

final readonly class ForceDeletePostCommand
{
    public function __construct(public int $id) {}
}
