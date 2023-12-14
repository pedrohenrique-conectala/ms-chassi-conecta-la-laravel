<?php

namespace Conectala\CacheWrapper\Handlers\Reflection;

use Illuminate\Support\Str;
use Laravel\SerializableClosure\Support\ReflectionClosure;

class ReflectionOriginClosureAttributes
{
    private array $classAttributes;
    private array $methodAttributes;

    private int|bool $closureStartLine;

    private \ReflectionClass|null $closureScopeClass;

    private \ReflectionMethod|null $closureScopeMethod;

    public function __construct(private readonly \Closure $closure, private readonly array|null $attributesMap = [])
    {
        $reflectionClosure = new ReflectionClosure($this->closure);
        $this->closureStartLine = $reflectionClosure->getStartLine();
        $this->closureScopeClass = $reflectionClosure->getClosureScopeClass();
    }

    /**
     * @param array|string|null $mapName
     * @return \ReflectionAttribute[]
     */
    public function getMethodAttributes(array|string|null $mapName = null): array
    {
        $this->methodAttributes = $this->methodAttributes ?? $this->loadMethodAttributes();
        return array_filter($this->methodAttributes, function (\ReflectionAttribute $attr) use ($mapName) {
            return Str::contains(implode('|', $mapName), $attr->getName());
        });
    }

    /**
     * @param array|string|null $mapName
     * @return \ReflectionAttribute[]
     */
    public function getClassAttributes(array|string|null $mapName = null): array
    {
        $this->classAttributes = $this->classAttributes ?? $this->loadClassAttributes();
        return array_filter($this->classAttributes, function (\ReflectionAttribute $attr) use ($mapName) {
            return Str::contains(implode('|', $mapName), $attr->getName());
        });
    }

    /**
     * @return \ReflectionAttribute[]
     */
    public function loadClassAttributes(): array
    {
        return $this->filterAttributes($this->closureScopeClass->getAttributes());
    }

    /**
     * @return \ReflectionAttribute[]
     */
    protected function loadMethodAttributes(): array
    {
        $methods = $this->filterMethods();
        if (empty($methods)) return [];
        $this->closureScopeMethod = current($methods);
        return $this->filterAttributes($this->closureScopeMethod->getAttributes());
    }

    /**
     * @return \ReflectionMethod[]
     */
    protected function filterMethods(): array
    {
        return array_filter($this->closureScopeClass->getMethods() ?? [], function (\ReflectionMethod $method) {
            return $method->getEndLine() >= $this->closureStartLine && $method->getStartLine() <= $this->closureStartLine;
        });
    }

    /**
     * @param \ReflectionAttribute[]|null $attributes
     * @return \ReflectionAttribute[]
     */
    protected function filterAttributes(array|null $attributes): array
    {
        return array_filter($attributes ?? [], function (\ReflectionAttribute $attr) {
            return empty($this->attributesMap) || Str::contains(implode(' | ', $this->attributesMap), $attr->getName());
        });
    }

    public function getScopeClass(): string
    {
        return $this->closureScopeClass->getName();
    }

    public function getScopeMethod(): string
    {
        return $this->closureScopeMethod->getName();
    }
}
