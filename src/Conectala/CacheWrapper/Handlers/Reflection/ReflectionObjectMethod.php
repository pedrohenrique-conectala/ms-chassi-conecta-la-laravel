<?php

namespace Conectala\CacheWrapper\Handlers\Reflection;

/**
 * Class CacheObjectMethod
 * @package Conectala\CacheWrapper\Handlers\Cache\Reflection
 */
class ReflectionObjectMethod extends ReflectionObject
{
    private \ReflectionMethod $reflectionMethod;

    public function __construct(
        string $className,
        string $methodName
    )
    {
        $this->reflectionMethod = new \ReflectionMethod($className, $methodName);
        $this->reflectionParameters = $this->reflectionMethod->getParameters();
        parent::__construct();
    }

    /**
     * @return array|\ReflectionAttribute[]
     */
    protected function getReflectionAttributes(): array
    {
        return $this->reflectionMethod->getAttributes();
    }
}
