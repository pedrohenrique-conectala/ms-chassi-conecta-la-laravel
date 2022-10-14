<?php

namespace Conectala\MultiTenant;

use Illuminate\Support\Facades\Facade;
use Conectala\MultiTenant\TenantManager;

class TenantFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return TenantManager::class;
    }
}
