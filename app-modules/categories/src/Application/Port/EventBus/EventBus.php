<?php

declare(strict_types=1);

namespace Modules\Categories\Application\Port\EventBus;

interface EventBus
{
    /** @param list<object> $events */
    public function publish(array $events): void;
}
