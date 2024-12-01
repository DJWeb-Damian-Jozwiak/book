<?php

declare(strict_types=1);

namespace DJWeb\Framework\Cache\Contracts;

interface StorageContract
{
    public function get(string $key): ?array;
    public function set(string $key, array $data): bool;
    public function delete(string $key): bool;
    public function clear(): bool;

    public function maxCapacity(int $size): void;
}
