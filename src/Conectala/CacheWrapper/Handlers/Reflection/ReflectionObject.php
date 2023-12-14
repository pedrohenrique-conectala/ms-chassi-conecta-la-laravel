<?php

namespace Conectala\CacheWrapper\Handlers\Reflection;

use Illuminate\Support\Str;

/**
 * Class ReflectionObject
 * @package Conectala\CacheWrapper\Handlers\Reflection
 * @property \ReflectionParameter[] $reflectionParameters
 */
abstract class ReflectionObject
{

    /**
     * @var \ReflectionAttribute[]
     */
    protected array $reflectionAttributes;

    public function __construct()
    {
        $this->reflectionAttributes = array_map(function (\ReflectionAttribute $attr) {
            $attr->newInstance();
            return $attr;
        }, $this->getReflectionAttributes());
    }

    /**
     * @return \ReflectionAttribute[]
     */
    protected abstract function getReflectionAttributes(): array;

    /**
     * @return \ReflectionParameter[]
     */
    public function getParameters(): array
    {
        return $this->reflectionParameters ?? [];
    }

    /**
     * @param string|null $name
     * @return \ReflectionAttribute[]
     */
    public function getAttributes(?string $name = null): array
    {
        if (is_null($name)) {
            return $this->reflectionAttributes;
        }
        return array_filter($this->reflectionAttributes, fn(\ReflectionAttribute $reflectionAttribute) => (
        Str::contains($reflectionAttribute->getName(), $name)
        ));
    }

}
