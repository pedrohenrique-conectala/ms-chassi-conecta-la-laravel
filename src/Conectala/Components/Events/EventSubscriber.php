<?php

namespace Conectala\Components\Events;

abstract class EventSubscriber implements EventSubscriberContract
{
    public function __construct(protected object $payload, protected \Closure $resolve, protected \Closure $reject)
    {
    }

    public function payload(): mixed
    {
        return $this->payload;
    }

    public function resolve(mixed ...$args): void
    {
        $resolve = $this->resolve;
        $resolve(...$args);
    }

    public function reject(mixed ...$args): void
    {
        $reject = $this->reject;
        $reject(...$args);
    }

    public abstract function handler(): void;
}
