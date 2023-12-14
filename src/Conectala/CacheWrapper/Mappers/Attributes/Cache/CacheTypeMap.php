<?php

namespace Conectala\CacheWrapper\Mappers\Attributes\Cache;

use Illuminate\Support\Str;
use Conectala\CacheWrapper\Mappers\Attributes\Cache\DataStructures\CacheAttribute;

/**
 * Class CacheTypeMap
 * @package Conectala\CacheWrapper\Mappers\Attributes
 */
class CacheTypeMap
{
    protected array $argumentsMap = [];

    public function __construct()
    {
    }

    public function getArgumentsMap(): array
    {
        return $this->argumentsMap;
    }

    public function resolve(string $name, int $position, array $mappedArguments, ?array $addMapParams = []): CacheAttribute
    {
        $cacheAttribute = new CacheAttribute($name, $position, $mappedArguments, $addMapParams);
        $keyParts = explode(':', $cacheAttribute->getOriginalName());
        $nameParts = explode('|', $keyParts[0] ?? '');
        $hasDefault = explode('=', $nameParts[0] ?? '');
        $name = $hasDefault[0] ?? '';
        $defaultValue = $hasDefault[1] ?? null;
        $cacheAttribute->aliasName = $nameParts[1] ?? $name;
        $cacheAttribute->name = empty($name) ? $cacheAttribute->aliasName : $name;
        if (str_starts_with($cacheAttribute->name, '!')) {
            $cacheAttribute->aliasName = $cacheAttribute->name = substr($cacheAttribute->name, 1);
            $cacheAttribute->mergedArguments = array_merge($cacheAttribute->mergedArguments, [
                $cacheAttribute->name => null, $cacheAttribute->originalName => null
            ]);
        }
        $cacheAttribute->originalValue = $cacheAttribute->getValueArgumentByArgName($cacheAttribute->name, $defaultValue);
        $cacheAttribute->value = ($keyParts[1] ?? $cacheAttribute->originalValue);
        if (Str::contains($cacheAttribute->value, '{value}')) {
            $cacheAttribute->value = Str::replace(['{value}'], $cacheAttribute->originalValue, $cacheAttribute->value);
            $cacheAttribute->value = trim(implode(':', [$cacheAttribute->aliasName, $cacheAttribute->value]), ':');
        }
        if (Str::contains($cacheAttribute->value, sprintf("{%s}", $cacheAttribute->aliasName))) {
            $cacheAttribute->value = Str::replace([
                sprintf("{%s}", $cacheAttribute->aliasName)
            ], $cacheAttribute->aliasName, $cacheAttribute->value);
        }
        return $cacheAttribute;
    }
}
