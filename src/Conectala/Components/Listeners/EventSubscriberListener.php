<?php

namespace Conectala\Components\Listeners;

use Conectala\Components\Events\EventSubscriber;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class EventSubscriberListener
{
    protected EventSubscriber $eventSubscriber;

    public function __invoke(string $event, object $payload, \Closure $resolve, \Closure $reject)
    {
        $this->handle(...func_num_args());
    }

    public function handle(string $event, object $payload, \Closure $resolve, \Closure $reject): void
    {
        try {
            $this->eventSubscriber = App::makeWith($event, ['payload' => $payload, 'resolve' => $resolve, 'reject' => $reject]);
            $this->eventSubscriber->handler();
            $this->eventSubscriber->resolve([]);
        } catch (\Exception $e) {
            $this->eventSubscriber->reject([]);
        }
    }

}
