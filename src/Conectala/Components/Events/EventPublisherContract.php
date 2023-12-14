<?php

namespace Conectala\Components\Events;

interface EventPublisherContract
{
    public function getPublisherClass(): string;

    public function getPayload(): mixed;
}
