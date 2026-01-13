<?php

declare(strict_types=1);

namespace Modules\Post\Application\Ports\Clock;

use DateTimeImmutable;

interface Clock
{
    public function now(): DateTimeImmutable;
}
