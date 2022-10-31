<?php

namespace Conectala\MultiTenant\Middleware;

use Conectala\MultiTenant\Repositories\TenantClientRepository;
use Conectala\MultiTenant\TenantFacade as Tenant;
use Closure;
use Illuminate\Http\Request;

class TenantConnection
{
    /**
     * @var TenantClientRepository Repository tenants.
     */
    private TenantClientRepository $tenantClientRepository;

    public function __construct()
    {
        $this->tenantClientRepository = new TenantClientRepository();
    }

    /**
     * Handle an incoming request.
     *
     * @todo chamar cache de configuração, para não ir sempre no banco de dados.
     *
     * @param   Request $request
     * @param   Closure $next
     * @return  mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // Chamar cache para ganho de tempo.
        $tenantClient = app('db')->select("SELECT * FROM `tenant_clients` WHERE `tenant` = '" . getTenantRequest() . "';");
        if (!empty($tenantClient)) {
            $tenantClient = $tenantClient[0];
            Tenant::setTenant($tenantClient);
        } else {
            if (str_contains($request->getRequestUri(), '/api')) {
                return response()->json(["error" => ["Tenant not found"]], 403);
            }
            abort(403, "Tenant not found");
        }
        return $next($request);
    }
}

