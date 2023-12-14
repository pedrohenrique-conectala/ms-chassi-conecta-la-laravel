<?php

namespace Conectala\CacheWrapper\Handlers\Cache;

use Conectala\CacheWrapper\Handlers\Arguments\Arguments;
use Conectala\CacheWrapper\Handlers\Reflection\ReflectionObjectMethod;

class CacheObjectMethodHandler extends CacheObjectHandler
{
    public function __construct(
        private readonly string    $className,
        private readonly string    $methodName,
        private readonly Arguments $methodArguments,
        private readonly ?array    $addCacheMapParams = []
    )
    {
        parent::__construct(
            new ReflectionObjectMethod($this->className, $this->methodName),
            $this->methodArguments,
            $this->addCacheMapParams
        );
    }
}
