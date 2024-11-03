<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Uri;

class Query
{
    public static function get(): string
    {
        return $_SERVER['QUERY_STRING'] ?? false ? '?' . $_SERVER['QUERY_STRING'] : '';
    }
}
