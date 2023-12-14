<?php

namespace Conectala\CacheWrapper\Handlers\Cache;

use Conectala\CacheWrapper\Handlers\Arguments\Arguments;
use Conectala\CacheWrapper\Handlers\Reflection\ReflectionObjectClass;

class CacheObjectClassHandler extends CacheObjectHandler
{
    public function __construct(
        private readonly string    $className,
        private readonly Arguments $methodArguments,
        private readonly ?array    $addCacheMapParams = []
    )
    {
        parent::__construct(
            new ReflectionObjectClass($this->className),
            $this->methodArguments,
            $this->addCacheMapParams
        );
    }
}
