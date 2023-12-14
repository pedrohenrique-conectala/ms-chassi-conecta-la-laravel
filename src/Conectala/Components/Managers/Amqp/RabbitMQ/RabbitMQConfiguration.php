<?php

namespace Conectala\Components\Managers\Amqp\RabbitMQ;

use Conectala\Components\Managers\Amqp\AmqpConfiguration;

class RabbitMQConfiguration extends AmqpConfiguration
{
    public function getPublishersNamespaces(): array
    {
        return [
            "\App\Components\Publishers\Amqp\RabbitMQ\\",
            "\Conectala\Components\Publishers\Amqp\RabbitMQ\\"
        ];
    }

    public function getSubscribersNamespaces(): array
    {
        return [
            "\App\Components\Subscribers\Amqp\RabbitMQ\\",
            "\Conectala\Components\Subscribers\Amqp\RabbitMQ\\",
            "\Conectala\Components\Subscribers\Amqp\RabbitMQ\RabbitMQ"
        ];
    }
}
