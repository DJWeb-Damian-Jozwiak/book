<?php

declare(strict_types=1);

namespace DJWeb\Framework\View\Engines;

use DJWeb\Framework\View\Contracts\RendererContract;

abstract class BaseAdapter implements RendererContract
{
    public function clearCache(string $cache_path): void
    {
        if (is_dir($cache_path)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($cache_path, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
foreach ($files as $file) {
                if ($file->isDir()) {
                    rmdir($file->getRealPath());

                } else {
                    unlink($file->getRealPath());

                }

}

        }
    }

}
