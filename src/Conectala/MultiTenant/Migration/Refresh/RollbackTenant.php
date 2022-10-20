<?php

namespace Conectala\MultiTenant\Migration\Refresh;

use Conectala\MultiTenant\Models\TenantClient;
use Conectala\MultiTenant\TenantFacade as Tenant;

class RollbackTenant
{
    /**
     * Execute the console command.
     */
    public static function handle($command)
    {
        if ($command->option('option') === 'system') {

        } else if ($command->option('option') === 'tenant') {

        }
    }
}
