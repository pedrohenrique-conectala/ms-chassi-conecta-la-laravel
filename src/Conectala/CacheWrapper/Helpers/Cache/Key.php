<?php

namespace Conectala\CacheWrapper\Helpers\Cache;

class Key extends Parts
{
    public static function build(array $parameters): string
    {
        if (class_exists('\App\Helpers\Cache\Key')) {
            return ($class = '\App\Helpers\Cache\Key')::build($parameters);
        }
        if (class_exists('\Conectala\MultiTenant\Helpers\Cache\Key')) {
            return ($class = '\Conectala\MultiTenant\Helpers\Cache\Key')::build($parameters);
        }
        return parent::build($parameters);
    }
}
