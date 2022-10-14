<?php

namespace Conectala\MultiTenant;

use Conectala\MultiTenant\Migration\Models\TenantClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;

class TenantManager
{
    private $tenant;

    /**
     * @return TenantClient
     */
    public function getTenant(): ?TenantClient
    {
        return $this->tenant;
    }

    /**
     * @param TenantClient $tenant
     */
    public function setTenant(?TenantClient $tenant): void
    {
        $this->tenant = $tenant;
        $this->makeTenantConnection();
    }

    private function makeTenantConnection()
    {
        $clone = config('database.connections.system');
        $clone['database'] = $this->tenant->database;
        Config::set('database.connections.tenant', $clone);
        DB::reconnect('tenant');
    }

    public function loadConnections()
    {
        if (Schema::hasTable((new TenantClient())->getTable())) {
            $tenantClients = TenantClient::all();
            foreach ($tenantClients as $tenantClient) {
                $clone = config('database.connections.system');
                $clone['database'] = $tenantClient->database;
                Config::set("database.connections.$tenantClient->tenant", $clone); //tenantClient_1
            }
        }
    }
}
