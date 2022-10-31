<?php

namespace Conectala\MultiTenant;

use Conectala\MultiTenant\Models\TenantClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;

class TenantManager
{
    private $tenant;

    /**
     * @return object|null
     */
    public function getTenant(): ?object
    {
        return $this->tenant;
    }

    /**
     * @param object|null $tenant
     */
    public function setTenant(?object $tenant): void
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
