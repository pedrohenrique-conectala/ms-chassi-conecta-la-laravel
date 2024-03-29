<?php

namespace Conectala\MultiTenant\Models\Traits;

use Conectala\MultiTenant\Helpers\Formatters\Str;

trait Common
{
    public function slugName(): string
    {
        return Str::camelCaseSlugify((new \ReflectionClass(static::class))->getShortName());
    }
}