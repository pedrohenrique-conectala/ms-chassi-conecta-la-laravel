<?php

namespace Conectala\CacheWrapper\Handlers\Cache\Reflection;

use Conectala\CacheWrapper\Handlers\Arguments\Arguments;
use Conectala\CacheWrapper\Handlers\Arguments\MethodArgument;
use Conectala\CacheWrapper\Handlers\Arguments\MethodArguments;
use Conectala\CacheWrapper\Mappers\Attributes\Cache\CacheTypeMap;
use Conectala\CacheWrapper\Mappers\Attributes\Cache\DataStructures\CacheAttribute;
use Conectala\CacheWrapper\Mappers\Attributes\Cache\DataStructures\CacheAttributes;

/**
 * Class ReflectionCacheKeyAttribute
 * @package Conectala\CacheWrapper\Handlers\Cache\Reflection
 * @property \ReflectionParameter[] $reflectionParameters
 * @property CacheTypeMap $cacheTypeMap
 */
class ReflectionCacheAttribute
{

    private array $mappedArguments;

    private MethodArguments $methodArguments;
    private CacheAttributes $cacheAttributes;
    private CacheTypeMap $cacheTypeMap;

    public function __construct(
        private readonly \ReflectionAttribute $reflectionAttribute,
        private readonly array                $reflectionParameters,
        private readonly Arguments            $methodArgs,
        private readonly ?array               $addMapParams
    )
    {
        $this->methodArguments = new MethodArguments();
        $this->cacheAttributes = new CacheAttributes();

        $this->cacheTypeMap = $this->reflectionAttribute->newInstance();
        $this->resolve();
    }

    protected function resolve(): void
    {
        $this->mapArgumentsWithParameters();
        $this->resolveAttributesWithArguments();
    }

    protected function mapArgumentsWithParameters(): void
    {
        foreach ($this->reflectionParameters as $k => $reflectionParameter) {
            try {
                $this->methodArguments->add(
                    new MethodArgument(
                        $reflectionParameter,
                        $this->methodArgs
                    )
                );
            } catch (\Throwable $e) {
                throw new \Exception('', 0, $e);
            }
        }
        $this->mappedArguments = $this->methodArguments->mapByNameValue();
    }

    protected function resolveAttributesWithArguments(): void
    {
        if (!($this->cacheTypeMap instanceof CacheTypeMap)) return;

        $args = $this->cacheTypeMap->getArgumentsMap();
        array_walk($args, function ($arg) {
            $arg = is_array($arg) ? $arg : [$arg];
            foreach ($arg as $position => $name) {
                $this->cacheAttributes->add($this->cacheTypeMap->resolve(
                    $name,
                    $position,
                    $this->mappedArguments,
                    $this->addMapParams
                ));
            }
        });
    }

    /**
     * @return CacheAttribute[]
     */
    public function getMappedAttributes(): array
    {
        return $this->cacheAttributes->getCacheAttributes();
    }
}
