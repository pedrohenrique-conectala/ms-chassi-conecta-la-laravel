<?php

namespace Conectala\Components\Listeners;

use Conectala\Components\Events\EventApplication;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\EnumeratesValues;

class EventApplicationListener
{
    protected static EventApplicationListener $instance;

    protected Collection $events;

    private function __construct()
    {
        $this->events = new Collection();
    }

    protected static function instance(): static
    {
        if (!isset(static::$instance) || static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public static function register(EventApplication $event): static
    {
        if (!static::instance()->has($event)) {
            static::instance()->events->add($event);
        }
        return static::instance();
    }

    public static function dispatch(EventApplication $event, mixed ...$args): void
    {
        if (static::instance()->has($event)) {
            $events = static::instance()->get($event);
            foreach ($events->all() as $k => $event) {
                $event->handle(...$args);
                static::forget($event);
            }
        }
    }

    public static function dispatchAll(): void
    {
        foreach (static::instance()->events->all() as $k => $event) {
            $event->handle();
            static::forget($event);
        }
    }

    public static function forget(EventApplication $eventApplication): void
    {
        $eventsApplication = static::instance()->get($eventApplication);
        /**
         * @var EventApplication $eventApplication
         */
        foreach ($eventsApplication->all() as $eventApplication) {
            /**
             * @var EventApplication $event
             */
            foreach (static::instance()->events->all() as $k => $event) {
                if ($eventApplication->eventId() === $event->eventId()) {
                    static::instance()->events->forget($k);
                }
            }
        }
    }

    protected function has(EventApplication $event): bool
    {
        return static::instance()->events->contains(function (EventApplication $eventApplication) {
            return $eventApplication->eventId();
        }, '=', $event->eventId());
    }

    protected function get(EventApplication $event): Collection
    {
        return static::instance()->events->where(function (EventApplication $eventApplication) {
            return $eventApplication->eventId();
        }, '=', $event);
    }
}
