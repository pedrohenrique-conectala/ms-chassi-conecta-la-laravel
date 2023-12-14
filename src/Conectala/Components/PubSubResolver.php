<?php

namespace Conectala\Components;

class PubSubResolver
{
    const PUBSUB_SELLERCENTER = 'sellercenter';
    const PUBSUB_RABBITMQ = 'rabbitmq';
    const PUBSUB_ORACLEAQ = 'oracleaq';

    public static function publisherReceiver(): string
    {
        if (env('REDIRECT_SELLERCENTER', true) ?? true) {
            return PubSubResolver::PUBSUB_SELLERCENTER;
        }
        return env('AMQP_RECEIVER_SERVICE', PubSubResolver::PUBSUB_SELLERCENTER) ?? PubSubResolver::PUBSUB_SELLERCENTER;
    }

    public static function subscriberProvider(): string
    {
        if (env('REDIRECT_SELLERCENTER', true) ?? true) {
            return PubSubResolver::PUBSUB_SELLERCENTER;
        }
        return env('AMQP_PROVIDER_SERVICE', PubSubResolver::PUBSUB_SELLERCENTER) ?? PubSubResolver::PUBSUB_SELLERCENTER;
    }
}
