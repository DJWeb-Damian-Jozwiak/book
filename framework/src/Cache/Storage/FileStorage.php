<?php

declare(strict_types=1);

namespace DJWeb\Framework\Cache\Storage;

use DJWeb\Framework\Cache\Contracts\StorageContract;
use DJWeb\Framework\Storage\Directory;

class FileStorage implements StorageContract
{
    private array $accessLog;
    private int $maxItems;
    public function __construct(private readonly string $directory, private readonly string $metaFile = 'meta.json')
    {
        Directory::ensureDirectoryExists($this->directory);
        $this->accessLog = [];
        $this->maxItems = 1000;
        $this->loadMeta();
    }

    public function get(string $key): ?array
    {
        $file = $this->getPath($key);
        return file_exists($file) ? unserialize(file_get_contents($file)) : null;
    }

    public function set(string $key, array $data): bool
    {
        if (count($this->accessLog) >= $this->maxItems) {
            $this->evictLRU();
        }

        $this->accessLog[$key] = time();
        $this->saveMeta();
        return (bool)file_put_contents($this->getPath($key), serialize($data));
    }

    public function delete(string $key): bool
    {
        $file = $this->getPath($key);
        return !file_exists($file) || unlink($file);
    }

    public function clear(): bool
    {
        array_map('unlink', glob($this->directory . '/*.cache'));
        return true;
    }

    private function getPath(string $key): string
    {
        return $this->directory . '/' . md5($key) . '.cache';
    }

    public function maxCapacity(int $size): void {
        $this->maxItems = $size;
        while ($this->getCurrentItemCount() > $size) {
            $this->evictLRU();
        }
    }

    private function loadMeta(): void
    {
        $metaPath = $this->directory . '/' . $this->metaFile;
        if (file_exists($metaPath)) {
            $this->accessLog = json_decode(file_get_contents($metaPath), true);
        }
    }

    private function saveMeta(): void
    {
        file_put_contents(
            $this->directory . '/' . $this->metaFile,
            json_encode($this->accessLog)
        );
    }
    private function evictLRU(): void
    {
        asort($this->accessLog);
        $key = array_key_first($this->accessLog);
        $this->delete($key);
    }
    private function getCurrentItemCount(): int {
        return count($this->accessLog);
    }
}