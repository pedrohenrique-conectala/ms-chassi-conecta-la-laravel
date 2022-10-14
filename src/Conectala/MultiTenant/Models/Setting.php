<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

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

    public function getAll()
    {
        return $this->get();
    }

    public function removeAll(): ?bool
    {
        return $this->query()->delete();
    }

}
