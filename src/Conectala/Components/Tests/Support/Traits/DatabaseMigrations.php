<?php

namespace Conectala\Components\Tests\Support\Traits;

use Illuminate\Support\Facades\Artisan;

trait DatabaseMigrations
{
    /**
     * Run the database migrations for the application.
     *
     * @return void
     */
    public function runDatabaseMigrations()
    {
        Artisan::call('migrate:fresh --path=/database/migrations/tenant', []);
        $this->beforeApplicationDestroyed(function () {
            Artisan::call('migrate:rollback --path=/database/migrations/tenant', []);
        });
    }
}
