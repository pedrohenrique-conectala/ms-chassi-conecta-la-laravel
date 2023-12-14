<?php

namespace Conectala\CacheWrapper\Handlers\Reflection;

class ReflectionObjectClass extends ReflectionObject
{
    private \ReflectionClass $reflectionClass;

    /**
     * @throws \ReflectionException
     */
    public function __construct(
        private readonly string $className
    )
    {
        $this->reflectionClass = new \ReflectionClass($this->className);
        parent::__construct();
    }

    protected function getReflectionAttributes(): array
    {
        return $this->reflectionClass->getAttributes();
    }

}
