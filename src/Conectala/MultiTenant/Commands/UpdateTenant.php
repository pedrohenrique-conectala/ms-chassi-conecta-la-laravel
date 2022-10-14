<?php

namespace App\Console\Commands;

use Conectala\MultiTenant\Migration\Refresh\UpdateTenant as Update;
use Illuminate\Console\Command;

class UpdateTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:update {--tenants= : Tenant of tenants to create structure} {--option= : tenant|system}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update migrations tenants';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Update::handle($this);
    }
}
