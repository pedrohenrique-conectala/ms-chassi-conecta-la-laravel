<?php

namespace Conectala\Components\Managers\Amqp\OracleAQ;

use Conectala\Components\Managers\Amqp\AmqpConfiguration;
use Conectala\Components\Managers\Amqp\AmqpManagerConfigurationContract;

class OracleAQManagerConfiguration implements AmqpManagerConfigurationContract
{
    protected array $configurationDefinition = [];

    public function __construct(AmqpConfiguration $amqpContextDefinition)
    {

    }

    public function handler(): void
    {
        $this->reloadDefinitions($this->configurationDefinition);
    }

    public function loadedDefinitions(): array
    {
        return [];
    }

    public function reloadDefinitions(array $definitions = []): void
    {

    }
}
