<?php

namespace Conectala\MultiTenant\Repositories;

use Conectala\MultiTenant\Models\TenantClient;

class TenantClientRepository extends AbstractRepository
{
    /**
     * @var TenantClient $model
     */
    protected mixed $model = TenantClient::class;

    public function getAll()
    {
        return $this->model->getAll();
    }

    public function getByTenant(string $tenant)
    {
        return $this->model->getByTenant($tenant);
    }
}
