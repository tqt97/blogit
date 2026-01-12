<?php

declare(strict_types=1);

namespace Modules\Post\Application\Commands;

final readonly class PublishPostNowCommand
{
    public function __construct(public int $id) {}
}
