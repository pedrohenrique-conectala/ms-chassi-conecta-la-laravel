<?php

namespace Conectala\CacheWrapper\Mappers\Attributes\Cache;

use \Attribute;

/**
 * Class CacheTagMap
 * @package Conectala\CacheWrapper\Mappers\Attributes
 * @example Tag[] #[CacheTagMap(['param', 'param2:{value}', 'param3|alias:{value}'])] = "value|param2:value|alias:value"
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class CacheTagMap extends CacheTypeMap
{
    public function __construct(array|string $mappedTags)
    {
        $this->argumentsMap = is_array($mappedTags) ? $mappedTags : [$mappedTags];
        parent::__construct();
    }
}
