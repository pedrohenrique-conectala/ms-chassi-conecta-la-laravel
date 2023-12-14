<?php

namespace Conectala\CacheWrapper\Handlers\Cache;

use Conectala\CacheWrapper\Handlers\Arguments\Arguments;
use Conectala\CacheWrapper\Handlers\Reflection\ReflectionOriginClosureAttributes;
use Conectala\CacheWrapper\Mappers\Attributes\Cache\CacheKeyMap;
use Conectala\CacheWrapper\Mappers\Attributes\Cache\CacheTagMap;
use Conectala\CacheWrapper\Mappers\Attributes\Cache\CacheTypeMap;
use Conectala\CacheWrapper\Mappers\Attributes\Cache\DataStructures\CacheAttribute;
use Conectala\CacheWrapper\Mappers\Attributes\Cache\DataStructures\CacheAttributes;

class CacheHandler
{

    private array|null $keys = null;
    private array|null $tags = null;

    private CacheObjectMethodHandler|null $cacheObjectMethodHandler = null;
    private CacheObjectClassHandler|null $cacheObjectClassHandler = null;

    private ReflectionOriginClosureAttributes|null $reflectionOriginClosureAttributes = null;

    public function __construct(
        private readonly string|array  $keyMap,
        private readonly \Closure|null $closure = null,
        private object|string|null     $scope = null,
        private string|null            $scopeMethod = null,
    )
    {
        if (is_string($this->keyMap)) {
            $this->keys = [$this->keyMap];
        }
        if (is_a($this->closure, \Closure::class)) {
            $this->reflectionOriginClosureAttributes = new ReflectionOriginClosureAttributes($this->closure);
        }
        $this->scope = is_object($this->scope) ? get_class($this->scope) : $this->scope;
    }

    private function buildByMethod(): void
    {
        if ($this->cacheObjectMethodHandler !== null) return;

        $cacheObjectHandlerArgs = [];
        if ($this->scopeMethod !== null) {
            $scope = $this->scope;
            if (is_null($this->scope)) {
                $reflectionMethod = new \ReflectionMethod($this->scopeMethod);
                $scope = $reflectionMethod->getDeclaringClass()->getName();
                $this->scopeMethod = $reflectionMethod->getShortName();
            }
            $cacheObjectHandlerArgs = [
                $scope,
                $this->scopeMethod,
                new Arguments(...(array_is_list($this->keyMap) ? $this->keyMap : [])),
                (!array_is_list($this->keyMap) ? $this->keyMap : []),
            ];
        }
        if ($this->reflectionOriginClosureAttributes && !empty($this->reflectionOriginClosureAttributes->getMethodAttributes([
                CacheKeyMap::class,
                CacheTagMap::class
            ]))) {
            $cacheObjectHandlerArgs = [
                $this->reflectionOriginClosureAttributes->getScopeClass(),
                $this->reflectionOriginClosureAttributes->getScopeMethod(),
                new Arguments(...(array_is_list($this->keyMap) ? $this->keyMap : [])),
                (!array_is_list($this->keyMap) ? $this->keyMap : []),
            ];
        }
        if (empty($cacheObjectHandlerArgs)) return;
        $this->cacheObjectMethodHandler = new CacheObjectMethodHandler(...$cacheObjectHandlerArgs);
    }

    private function buildByClass(): void
    {
        if ($this->cacheObjectClassHandler !== null) return;
        $cacheObjectHandlerArgs = [];
        if ($this->scope !== null) {
            $cacheObjectHandlerArgs = [
                $this->scope,
                new Arguments(...(array_is_list($this->keyMap) ? $this->keyMap : [])),
                (!array_is_list($this->keyMap) ? $this->keyMap : []),
            ];
        }
        if ($this->reflectionOriginClosureAttributes && !empty($this->reflectionOriginClosureAttributes->getClassAttributes([
                CacheKeyMap::class,
                CacheTagMap::class
            ]))) {
            $cacheObjectHandlerArgs = [
                $this->reflectionOriginClosureAttributes->getScopeClass(),
                new Arguments(...(array_is_list($this->keyMap) ? $this->keyMap : [])),
                (!array_is_list($this->keyMap) ? $this->keyMap : []),
            ];
        }
        if (empty($cacheObjectHandlerArgs)) return;
        $this->cacheObjectClassHandler = new CacheObjectClassHandler(...$cacheObjectHandlerArgs);
    }

    public function keys(): array
    {
        return $this->keys ?? ($this->keys = $this->loadKeys());
    }

    protected function loadKeys(): array
    {
        $this->buildByMethod();
        if ($this->cacheObjectMethodHandler && !empty($this->cacheObjectMethodHandler->keys())) {
            return $this->cacheObjectMethodHandler->keys();
        }
        $this->buildByClass();
        if ($this->cacheObjectClassHandler && !empty($this->cacheObjectClassHandler->keys())) {
            return $this->cacheObjectClassHandler->keys();
        }
        $cacheTypeMap = new CacheTypeMap();
        $mappedAttrs = new CacheAttributes();
        $i = 0;
        foreach ($this->keyMap as $name => $value) {
            $mappedAttrs->add($cacheTypeMap->resolve(
                (string)$name,
                is_int($name) ? $name : $i++,
                $this->keyMap
            ));
        }
        return $this->mapLoadedAttrs(array_map(function (CacheAttribute $cacheAttribute) {
            return $cacheAttribute->getValueWithIdx();
        }, $mappedAttrs->getCacheAttributesAll()));
    }

    public function tags(): array
    {
        return $this->tags ?? ($this->tags = $this->loadTags());
    }

    protected function loadTags(): array
    {
        $this->buildByMethod();
        if ($this->cacheObjectMethodHandler && !empty($this->cacheObjectMethodHandler->tags())) {
            return $this->cacheObjectMethodHandler->tags();
        }
        $this->buildByClass();
        if ($this->cacheObjectClassHandler && !empty($this->cacheObjectClassHandler->tags())) {
            return $this->cacheObjectClassHandler->tags();
        }
        return [];
    }

    protected function mapLoadedAttrs(array $loadedAttrs = []): array
    {
        $keys = array_keys($loadedAttrs);
        $namedKeys = array_filter($keys, 'is_string');
        if (count($keys) === count($namedKeys)) {
            return $loadedAttrs;
        }
        return array_reduce($loadedAttrs, 'array_merge', []);
    }
}
