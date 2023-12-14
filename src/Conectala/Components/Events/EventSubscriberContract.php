<?php

namespace Conectala\Components\Events;

interface EventSubscriberContract
{
    public function payload(): mixed;

    public function resolve(mixed ...$args): void;

    public function reject(mixed ...$args): void;
}
