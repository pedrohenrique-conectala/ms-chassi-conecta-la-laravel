<?php

namespace Conectala\MultiTenant\Helpers\Str\Support;

class Str extends \Illuminate\Support\Str
{
    /**
     * @param string $string
     * @param string $separator
     * @return string
     * @example string="camelCase" --> result="camel_case"
     */
    public static function camelCaseSlugify(string $string, string $separator = '_'): string
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', static::toASCII($string), $matches);
        return implode($separator, array_map(fn($part) => static::lower($part), $matches[0] ?? []));
    }

    public static function toASCII($string): string
    {
        return mb_convert_encoding(
            transliterator_transliterate('Any-Latin; Latin-ASCII', $string),
            'ASCII',
            'UTF-8'
        );
    }
}