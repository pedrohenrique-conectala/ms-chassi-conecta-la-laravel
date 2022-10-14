<?php

namespace App\Console\Commands;

use Conectala\MultiTenant\Migration\Refresh\SeedTenant as Seed;
use Illuminate\Console\Command;

class SeedTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:seed {--option= : tenant|system}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update seed tenants';

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
        Seed::handle($this);
    }
}
