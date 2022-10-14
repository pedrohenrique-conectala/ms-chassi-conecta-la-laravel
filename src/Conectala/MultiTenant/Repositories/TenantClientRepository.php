<?php

namespace App\Repositories;

use App\Models\TenantClient;

class TenantClientRepository extends AbstractRepository
{
    /**
     * @var TenantClient $model
     */
    protected mixed $model = TenantClient::class;
}
