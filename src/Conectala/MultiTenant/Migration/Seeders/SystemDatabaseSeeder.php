<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SystemDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(TenantClientsTableSeeder::class);
    }
}
