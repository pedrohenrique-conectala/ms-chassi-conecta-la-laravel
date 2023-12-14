<?php

namespace Conectala\Components\Listeners;

use Conectala\Components\Events\EventPublisher;
use Conectala\Components\Managers\PubSubManager;

class EventPublisherListener
{
    public function handle(EventPublisher $event): void
    {
        try {
            $publisher = PubSubManager::resolvePublisherConfiguration()
                ->resolvePublisherClassByEvent($event);
            $publisher->publish($event->getPayload());
        } catch (\Throwable $e) {
            
        }
    }
}
