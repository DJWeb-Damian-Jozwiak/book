<?php

declare(strict_types=1);

namespace DJWeb\Framework\Utils;

class File
{
    public static function create(string $path, string $content = '', int $mode = 0777): void
    {
        file_put_contents($path, $content);
        chmod($path, $mode);
    }

    /**
     * @param string $path
     * @param string $cached_file
     * @return bool
     * @codeCoverageIgnore
     */
    public static function isCached(string $path, string $cached_file): bool
    {
        if (! file_exists($cached_file)) {
            return false;
        }
        return filemtime($cached_file) >= filemtime($path);
    }

}
