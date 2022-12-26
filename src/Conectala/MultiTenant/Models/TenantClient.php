<?php

namespace Conectala\MultiTenant\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantClient extends Model
{
    use HasFactory;

    protected $connection = 'system';

    protected $fillable = [
        'name',
        'database',
        'tenant'
    ];

    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function getByTenant(string $tenant)
    {
        return $this->where('tenant', $tenant)->first();
    }
}
