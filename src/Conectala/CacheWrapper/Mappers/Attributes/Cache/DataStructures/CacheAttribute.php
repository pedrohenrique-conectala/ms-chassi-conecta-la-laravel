<?php

namespace Conectala\CacheWrapper\Mappers\Attributes\Cache\DataStructures;

class CacheAttribute
{
    public string $name;
    public string|null $aliasName;
    public string|null $value;

    public string|null $originalValue;

    public array $mergedArguments;

    private bool $argMappingFound = true;

    public function __construct(
        public readonly string $originalName,
        public readonly int    $position,
        public readonly array  $mappedArguments,
        public readonly ?array $addArguments = []
    )
    {
        $this->mergedArguments = array_merge($this->mappedArguments, $this->addArguments);
    }

    public function getValue(): string|int|bool|null
    {
        return $this->value;
    }

    public function getValueWithIdx(): array
    {
        return [$this->name => $this->getValue()];
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function found(): bool
    {
        return $this->argMappingFound;
    }

    public function getValueArgumentByArgName(string $argName, string|int|bool|null $defaultValue = null): string|int|bool|null
    {
        $this->argMappingFound = array_key_exists($argName, $this->mergedArguments) || !empty($defaultValue);
        return $this->mergedArguments[$argName] ?? $this->mergedArguments[$this->originalName] ?? $defaultValue ?? null;
    }
}
