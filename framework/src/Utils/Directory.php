<?php

declare(strict_types=1);

namespace DJWeb\Framework\Utils;

class Directory
{
    public static function create(string $path): void
    {
        if (! is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }
}
