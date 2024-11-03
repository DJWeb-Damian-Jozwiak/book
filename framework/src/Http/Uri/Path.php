<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Uri;

class Path
{
    public static function get(): string
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $queryPos = strpos($path, '?');
        if ($queryPos !== false) {
            $path = substr($path, 0, $queryPos);
        }

        return $path;
    }
}
