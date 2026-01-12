<?php

declare(strict_types=1);

namespace Modules\Tag\Application\Ports\EventBus;

interface EventBus
{
    /** @param list<object> $events */
    public function publish(array $events): void;
}
