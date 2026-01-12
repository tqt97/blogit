<?php

declare(strict_types=1);

namespace Modules\Post\Infrastructure\Bus\Events;

use Illuminate\Support\Facades\Event;
use Modules\Post\Application\Ports\EventBus\EventBus;

final class LaravelEventBus implements EventBus
{
    public function publish(array $events): void
    {
        foreach ($events as $event) {
            Event::dispatch($event);
        }
    }
}
