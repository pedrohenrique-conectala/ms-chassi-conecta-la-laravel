<?php

namespace Conectala\MultiTenant\Repositories;

use Conectala\CacheWrapper\Wrappers\Cache;
use Conectala\MultiTenant\Models\Setting;

class SettingRepository extends AbstractRepository
{
    /**
     * @var Setting $model
     */
    protected mixed $model = Setting::class;

    /**
     * @param string $name
     * @return Setting|null
     */
    public function getByName(string $name)
    {
        return Cache::remember([
            'name' => $name
        ], function () use ($name) {
            return $this->model->getByName($name);
        }, 3600, $this->model
        );
    }

    public function getAll()
    {
        return $this->model->getAll();
    }

    public function removeAll(): ?bool
    {
        return (bool)current(array_map(function ($param) {
            $model = (new Setting())->getById($param['id']);
            return $model->forceDelete();
        }, Setting::all(['id'])->toArray()));
    }
}
