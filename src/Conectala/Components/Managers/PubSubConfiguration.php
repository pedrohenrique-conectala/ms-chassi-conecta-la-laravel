<?php

namespace Conectala\Components\Managers;

abstract class PubSubConfiguration
{
    protected ManagerConfiguration $managerConfiguration;

    public function __construct(protected array   $addProperties = [],
                                protected ?array  $overrideProperties = null,
                                protected ?string $repositoryKey = null,
                                protected ?string $propertyUseKey = null)
    {
        $this->loadPublishersNamespaces();
        $this->loadSubscribersNamespaces();
        $this->managerConfiguration = new ManagerConfiguration(...[
            $this->repositoryKey,
            $this->propertyUseKey,
            $this->addProperties,
            $this->overrideProperties
        ]);
    }

    public function getProperty(string $key): mixed
    {
        return $this->managerConfiguration->getProperty($key);
    }

    protected function loadPublishersNamespaces(): void
    {
        $this->addProperties = array_merge_recursive($this->addProperties, [
            'namespaces' => [
                'publishers' => $this->getPublishersNamespaces()
            ]
        ]);
    }

    protected function loadSubscribersNamespaces(): void
    {
        $this->addProperties = array_merge_recursive($this->addProperties, [
            'namespaces' => [
                'subscribers' => $this->getSubscribersNamespaces()
            ]
        ]);
    }

    public abstract function getPublishersNamespaces(): array;

    public abstract function getSubscribersNamespaces(): array;

}
