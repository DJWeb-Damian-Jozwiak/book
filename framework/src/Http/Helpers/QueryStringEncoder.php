<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Helpers;

final class QueryStringEncoder
{
    public static function encodeQueryString(string $query): string
    {
        if ($query === '') {
            return '';
        }

        $parts = explode('&', $query);
        $encodedParts = [];

        foreach ($parts as $part) {
            if (str_contains($part, '=')) {
                [$key, $value] = explode('=', $part, 2);
                $encodedParts[] = rawurlencode($key) . '=' . rawurlencode(
                    $value
                );
            } else {
                $encodedParts[] = rawurlencode($part);
            }
        }

        return implode('&', $encodedParts);
    }
}
