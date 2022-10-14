<?php

namespace Conectala\MultiTenant\Migration\Repositories;

use Conectala\MultiTenant\Migration\Models\TenantClient;
use App\Repositories\AbstractRepository;

class TenantClientRepository extends AbstractRepository
{
    /**
     * @var TenantClient $model
     */
    protected mixed $model = TenantClient::class;
}
