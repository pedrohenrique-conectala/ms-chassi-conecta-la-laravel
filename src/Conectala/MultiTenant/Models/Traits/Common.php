<?php

namespace Conectala\MultiTenant\Models\Traits;

use Illuminate\Support\Str;

trait Common
{
    public function slugName(): string
    {
        return Str::snake((new \ReflectionClass(static::class))->getShortName());
    }
}