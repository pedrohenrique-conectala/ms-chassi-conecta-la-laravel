<?php

namespace Conectala\CacheWrapper\Mappers\Attributes\Cache\DataStructures;

class CacheAttributes
{

    /**
     * @var CacheAttribute[]
     */
    private array $cacheAttributes;

    public function __construct()
    {
        $this->cacheAttributes = [];
    }

    public function add(CacheAttribute $cacheAttribute): void
    {
        $this->cacheAttributes[] = $cacheAttribute;
    }

    /**
     * @return CacheAttribute[]
     */
    public function getCacheAttributes(): array
    {
        return array_filter($this->sort()->cacheAttributes, function (CacheAttribute $attr) {
            return $attr->found();
        });
    }

    /**
     * @return CacheAttribute[]
     */
    public function getCacheAttributesAll(): array
    {
        return $this->sort()->cacheAttributes;
    }

    protected function sort(): static
    {
        if ($this->count() <= 1) return $this;
        usort($this->cacheAttributes, function (CacheAttribute $first, CacheAttribute $second) {
            return $first->getPosition() > $second->getPosition();
        });
        return $this;
    }

    public function count(): int
    {
        return count($this->cacheAttributes);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }
}
