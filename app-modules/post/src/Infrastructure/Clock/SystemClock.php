<?php

declare(strict_types=1);

namespace Modules\Post\Infrastructure\Clock;

use DateTimeImmutable;
use Modules\Post\Application\Ports\Clock\Clock;

final class SystemClock implements Clock
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable;
    }
}
