<?php

namespace App\Models\Traits;

use App\Scopes\StoreScope;

trait Storetable
{
    protected static function bootStoretable()
    {
        static::addGlobalScope(new StoreScope());

        if (getStoreHeader()) {
            static::creating(function($model) {
                $model->store_id = getStoreHeader();
            });
        }
    }

}
