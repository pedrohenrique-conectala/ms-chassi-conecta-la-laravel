<?php

namespace Conectala\CacheWrapper\Mappers\Attributes\Cache;

use \Attribute;

/**
 * Class CacheKeyMap
 * @package Conectala\CacheWrapper\Mappers\Attributes
 * @example Key[] #[CacheKeyMap(['param', 'param=default', 'param2:{value}', 'param3|alias:{value}'])] = "value|param2:value|alias:value"
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class CacheKeyMap extends CacheTypeMap
{
    public function __construct(array|string $mappedKeys)
    {
        $this->argumentsMap = is_array($mappedKeys) ? $mappedKeys : [$mappedKeys];
        parent::__construct();
    }
}
