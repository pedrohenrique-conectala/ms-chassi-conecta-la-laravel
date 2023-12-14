<?php

namespace Conectala\Components\Managers\Amqp;

use Conectala\Components\Managers\ComponentManagerConfigurationContract;

interface AmqpManagerConfigurationContract extends ComponentManagerConfigurationContract
{
    public function __construct(AmqpConfiguration $amqpContextDefinition);
}
