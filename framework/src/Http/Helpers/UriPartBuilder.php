<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Helpers;

class UriPartBuilder
{
    public static function buildPart(
        string $result,
        callable $callable,
        string $prefix = '',
        string $suffix = '',
    ): string {
        if ($callable()) {
            return $result . $prefix . $callable() . $suffix;
        }
        return $result;
    }
}
