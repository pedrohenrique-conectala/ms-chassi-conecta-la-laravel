<?php

namespace Conectala\Components\Managers\Amqp\OracleAQ;

use Conectala\Components\Managers\Amqp\AmqpConfiguration;

class OracleAQConfiguration extends AmqpConfiguration
{
    public function getPublishersNamespaces(): array
    {
        return [
            "\App\Components\Publishers\Amqp\OracleAQ\\",
            "\Conectala\Components\Publishers\Amqp\OracleAQ\\"
        ];
    }

    public function getSubscribersNamespaces(): array
    {
        return [
            "\App\Components\Subscribers\Amqp\OracleAQ\\",
            "\Conectala\Components\Subscribers\Amqp\OracleAQ\\"
        ];
    }
}
