<?php

namespace Conectala\MultiTenant\Migration\Seed;

use Conectala\MultiTenant\TenantFacade as Tenant;

class SeedTenant extends Command
{
    /**
     * Execute the console command.
     */
    public function handle($command)
    {
        if (app()->environment() !== 'production') {
            if ($command->option('option') === 'system') {
                $command->warn("Running seed system");
                $command->call('db:seed', [
                    '--class' => 'SystemDatabaseSeeder'
                ]);
                $command->info('Seed system finished');
            } else if ($command->option('option') === 'tenant') {
                Tenant::loadConnections();
                $tenantClients = TenantClient::all();
                $command->info("Seed system running to tenants:" . implode(',', $tenantClients->pluck('tenant')->toArray()) . "\n");
                foreach ($tenantClients as $tenantClient) {
                    Tenant::setTenant($tenantClient);
                    $command->warn("Running seed on tenant:$tenantClient->tenant");
                    $command->call('db:seed', [
                        '--database' => $tenantClient->tenant,
                        '--class' => 'TenantDatabaseSeeder'
                    ]);
                    $command->info("Seed tenant:$tenantClient->tenant finished\n");
                }
                $command->info('Seeding tenant finished');
            }
        } else {
            $command->error('You are production');
        }
    }
}