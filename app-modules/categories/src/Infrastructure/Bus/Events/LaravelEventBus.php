<?php

declare(strict_types=1);

namespace Modules\Categories\Infrastructure\Bus\Events;

use Illuminate\Support\Facades\Event;
use Modules\Categories\Application\Port\EventBus\EventBus;

final class LaravelEventBus implements EventBus
{
    public function publish(array $events): void
    {
        foreach ($events as $event) {
            Event::dispatch($event);
        }
    }
}
