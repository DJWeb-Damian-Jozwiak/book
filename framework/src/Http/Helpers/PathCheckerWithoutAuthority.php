<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Helpers;

class PathCheckerWithoutAuthority
{
    public static function check(string $path, string $authority): bool
    {
        return $path === '/' && $authority === '';
    }
}
