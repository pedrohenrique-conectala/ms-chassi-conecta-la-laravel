<?php

namespace App\Repositories;

use App\Models\Setting;

class SettingRepository extends AbstractRepository
{
    /**
     * @var Setting $model
     */
    protected mixed $model = Setting::class;

    public function getByName(string $name)
    {
        return $this->model->getByName($name);
    }

    public function getAll()
    {
        return $this->model->getAll();
    }

    public function removeAll(): ?bool
    {
        return $this->model->removeAll();
    }
}
