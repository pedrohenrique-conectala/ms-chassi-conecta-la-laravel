<?php

namespace Conectala\MultiTenant\Models;

use Conectala\CacheWrapper\Mappers\Attributes\Cache\CacheKeyMap;
use Conectala\MultiTenant\Models\Traits\Common;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[CacheKeyMap(['!store_id', 'model=setting|resource:{value}', 'name|param:{value}'])]
class Setting extends Model
{
    use HasFactory, Common;

    protected $connection = 'tenant';

    protected $fillable = [
        'name',
        'value',
        'active'
    ];

    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function create(array $data)
    {
        return $this->insert($data);
    }

    public function getByName(string $name)
    {
        return $this->where('name', $name)->first();
    }

    public function getById(int $id): Setting
    {
        return $this->where('id', $id)->first();
    }

    public function getAll()
    {
        return $this->get();
    }

    public function removeAll(): ?bool
    {
        return $this->query()->delete();
    }

}
