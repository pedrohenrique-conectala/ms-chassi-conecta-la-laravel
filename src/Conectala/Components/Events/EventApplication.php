<?php

namespace Conectala\Components\Events;

class EventApplication
{

    protected mixed $args;

    public function __construct(protected array|string $event, protected ?\Closure $closure = null, mixed ...$args)
    {
        $this->args = $args;
    }

    public function handle(mixed ...$args): void
    {
        if ($this->closure instanceof \Closure) {
            if ($args[0] ?? null) {
                ($this->closure)(...$args);
                return;
            }
            ($this->closure)(...$this->args);
        }
    }

    public function eventId(): string
    {
        return !is_string($this->event) ? serialize($this->event) : $this->event;
    }
}
