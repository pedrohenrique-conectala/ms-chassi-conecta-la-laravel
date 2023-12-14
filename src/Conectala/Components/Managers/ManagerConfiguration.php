<?php

namespace Conectala\Components\Managers;

use Conectala\Components\Configurations\RepositoryConfiguration;

class ManagerConfiguration
{
    const REPOSITORY_KEY = 'app';

    protected array $properties = [];

    protected array $configData = [];

    public function __construct(
        protected ?string $repositoryKey = null,
        protected ?string $propertyUseKey = null,
        protected array   $addProperties = [],
        protected ?array  $overrideProperties = null,
    )
    {
        $this->extractProperties();
    }

    protected function extractProperties(): void
    {
        if (RepositoryConfiguration::has($this->repositoryKey ?? static::REPOSITORY_KEY)) {
            $this->configData = RepositoryConfiguration::get($this->repositoryKey ?? static::REPOSITORY_KEY);
            $this->propertyUseKey = $this->propertyUseKey ?? $this->configData['use'] ?? null;
            $this->properties = $this->prepareProperties();
        }
        $this->properties = $this->overrideProperties ?? array_merge_recursive($this->properties, $this->addProperties);
    }

    protected function prepareProperties(): array
    {
        if (!empty($properties = ($this->configData['properties'][$this->propertyUseKey] ?? $this->configData['properties'] ?? []))) {
            $properties = array_is_list($properties) ? (current($properties) ?: []) : (is_array(current($properties)) ? current($properties) : $properties) ?? [];
        }
        return !empty($properties) ? $properties : $this->configData;
    }

    public function getProperty(string $key)
    {
        return array_key_exists($key, $this->properties) ? $this->properties[$key] : null;
    }
}
