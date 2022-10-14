<?php

namespace Conectala\MultiTenant\Migration\Seeders;

use Conectala\MultiTenant\Migration\Models\TenantClient;
use Illuminate\Database\Seeder;

class TenantClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TenantClient::query()->delete();
        TenantClient::insert(
            [
                [
                    'name'      => "Ambiente Conecta Lá",
                    'database'  => "ms_shipping_conectala",
                    'tenant'    => "conectala"
                ]
            ]
        );
    }
}
