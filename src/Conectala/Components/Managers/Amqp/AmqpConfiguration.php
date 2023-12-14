<?php

namespace Conectala\Components\Managers\Amqp;

use Conectala\Components\Managers\PubSubConfiguration;

abstract class AmqpConfiguration extends PubSubConfiguration
{
    const REPOSITORY_KEY = 'amqp';

    public function __construct(
        protected array   $addProperties = [],
        protected ?array  $overrideProperties = null,
        protected ?string $repositoryKey = null,
        protected ?string $propertyUseKey = null
    )
    {
        parent::__construct($this->addProperties, $this->overrideProperties, $this->repositoryKey ?? static::REPOSITORY_KEY, $this->propertyUseKey);
    }

    public abstract function getPublishersNamespaces(): array;

    public abstract function getSubscribersNamespaces(): array;
}
