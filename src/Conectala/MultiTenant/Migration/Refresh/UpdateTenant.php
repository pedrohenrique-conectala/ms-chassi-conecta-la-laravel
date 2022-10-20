<?php

namespace Conectala\MultiTenant\Migration\Refresh;

use Conectala\MultiTenant\Models\TenantClient;
use Conectala\MultiTenant\TenantFacade as Tenant;

class UpdateTenant
{
    /**
     * Execute the console command.
     */
    public static function handle($command)
    {
        if ($command->option('option') === 'system') {
            if (app()->environment() !== 'production') {
                $command->warn("Running the migration on the system");
                $command->call('migrate', [
                    '--database' => 'system',
                    '--path' => 'database/migrations/system'
                ]);
                $command->info("Migration in the system finished\n");
            } else {
                $command->error('You are production');
            }
        } else if ($command->option('option') === 'tenant') {
            if ($command->option('tenants') === 'all') {
                $tenantClients = TenantClient::all();
                $tenants = $tenantClients->pluck('tenant')->toArray();
            } else {
                $tenants = explode(",", $command->option('tenants')); // conectala,decathlon,mesbla
            }

            // Carrega as conexões dos tenants.
            Tenant::loadConnections();
            $tenantClients = TenantClient::whereIn('tenant', $tenants)->get();
            $command->info("Running tenants:" . implode(',', $tenants) . "\n");

            // Ler todos os tenants e executar o migrate.
            foreach ($tenantClients as $tenantClient) {
                $command->info("Checking if the database exists:$tenantClient->database\n");
                DB::statement("CREATE DATABASE IF NOT EXISTS $tenantClient->database;");

                $params = [
                    '--database' => $tenantClient->tenant, // conexão decathlon
                    '--path' => 'database/migrations/tenant'
                ];

                /*
                if (app()->environment() !== 'production') {
                    $params[] = '--seed';
                }
                */

                // php artisan migrate --database --path --seed
                $command->warn("Running migration on tenant:$tenantClient->tenant");
                $command->call('migrate', $params);
                $command->info("Migration tenant:$tenantClient->tenant finished\n");
            }
            if (!$tenantClients->count()) {
                $command->error('Tenants of tenant not found in table.');
            }
        }
    }
}
