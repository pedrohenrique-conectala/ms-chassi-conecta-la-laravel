<?php

namespace Conectala\Components\Commands;

use Conectala\Components\Services\ManagerTenantService;
use Illuminate\Console\Command;

class TenantSubscribers extends Command
{
    protected $signature = 'tenant:subscribers {--resource= : Especific resource to consume} {--tenant= : Especific tenant do consume}';

    protected $description = 'Command for consuming message queues';

    public function __construct(protected ManagerTenantService $managerTenantService)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        if (!empty($this->option('tenant'))) {
            $this->info("Running command subscriber for resource '{$this->option('resource')}' and tenant '{$this->option('tenant')}'");
            $this->managerTenantService->allocateTenantSubscribers($this->option('tenant'), $this->option('resource') ?? null);
            return;
        }
        $this->info("Running command subscriber for all resources and tenants...");
        $this->managerTenantService->allocateSubscribers($this->option('resource') ?? null, $this->option('tenant') ?? null);
        return;
    }
}
