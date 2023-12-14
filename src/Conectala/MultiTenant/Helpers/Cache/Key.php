<?php

namespace Conectala\MultiTenant\Helpers\Cache;

use Conectala\CacheWrapper\Helpers\Cache\Parts;

class Key extends Parts
{
    public static function retrievePrefixParts(): array
    {
        try {
            $tenant = getTenantRequest();
        } catch (\Throwable $e) {
        }
        try {
            $store = getStoreRequest();
        } catch (\Throwable $e) {
        }
        return [
            'tenant' => $tenant ?? 'tenant',
            'store_id' => $store ?? null
        ];
    }
}
