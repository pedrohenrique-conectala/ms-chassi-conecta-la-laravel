<?php

namespace Conectala\Components\Events;

abstract class EventPublisher implements EventPublisherContract
{

    public function __construct(protected mixed $data)
    {
    }

    public abstract function getPublisherClass(): string;

    public function getPayload(): mixed
    {
        return $this->data;
    }
}
