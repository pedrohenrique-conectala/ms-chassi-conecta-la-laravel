<?php

namespace Conectala\CacheWrapper\Handlers\Cache;

use App\Helpers\Cache\Tag;
use Conectala\CacheWrapper\Handlers\Arguments\Arguments;
use Conectala\CacheWrapper\Handlers\Cache\Reflection\ReflectionCacheAttribute;
use Conectala\CacheWrapper\Handlers\Reflection\ReflectionObject;
use Conectala\CacheWrapper\Mappers\Attributes\Cache\CacheKeyMap;
use Conectala\CacheWrapper\Mappers\Attributes\Cache\CacheTagMap;
use Conectala\CacheWrapper\Mappers\Attributes\Cache\DataStructures\CacheAttribute;

class CacheObjectHandler
{

    protected array|null $keys = null;
    protected array|null $tags = null;

    public function __construct(
        private readonly ReflectionObject $reflectionObject,
        private readonly Arguments        $methodArguments,
        private readonly ?array           $addCacheMapParams = []
    )
    {
    }

    public function tags(): array
    {
        return $this->tags ?? array_map(function (array $tags) {
            return Tag::build($tags);
        }, $this->load(CacheTagMap::class));
    }

    public function keys(): array
    {
        return $this->keys ?? current($this->load(CacheKeyMap::class)) ?: [];
    }

    protected function load(string $attrMap): array
    {
        $methodAttributes = $this->reflectionObject->getAttributes($attrMap);
        $mappedAttrs = array_map(function (\ReflectionAttribute $reflectionAttribute) {
            return new ReflectionCacheAttribute(
                $reflectionAttribute,
                $this->reflectionObject->getParameters(),
                $this->methodArguments,
                $this->addCacheMapParams
            );
        }, $methodAttributes);

        return array_map(function (ReflectionCacheAttribute $mappedAttr) {
            return $this->mapLoadedAttrs(array_map(function (CacheAttribute $cacheAttribute) {
                    return $cacheAttribute->getValueWithIdx();
                }, $mappedAttr->getMappedAttributes())
            );
        }, $mappedAttrs);
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
