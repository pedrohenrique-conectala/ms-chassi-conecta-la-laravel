<?php

namespace Conectala\CacheWrapper\Helpers\Cache;

use Illuminate\Support\Facades\Facade;

class Parts extends Facade
{
    const SEPARATOR = "|";

    public static function build(array $parameters): string
    {
        return static::format(static::handleParts(array_merge(static::retrievePrefixParts(), $parameters, static::retrievePosfixParts())));
    }

    public static function retrievePrefixParts(): array
    {
        return [];
    }

    public static function retrievePosfixParts(): array
    {
        return [];
    }

    public static function handleParts(array $parts = []): array
    {
        return array_map(function ($item) {
            return $item;
        }, array_filter($parts, function ($item) {
                if ($item === null) return false;
                if (((int)$item) === 0) return true;
                if (!empty($item)) return true;
                return false;
            })
        );
    }

    public static function format(array $parts = []): string
    {
        return sprintf("%s", implode(static::SEPARATOR, $parts));
    }
}
