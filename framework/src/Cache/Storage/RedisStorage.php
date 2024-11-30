<?php

declare(strict_types=1);

namespace DJWeb\Framework\Cache\Storage;

use DJWeb\Framework\Cache\Contracts\StorageContract;
use DJWeb\Framework\Cache\RedisEvictionPolicy;
use Redis;

readonly class RedisStorage implements StorageContract
{
    public function __construct(
        private Redis  $redis,
        private string $prefix = 'cache:',
    ) {
        $this->setEvictionPolicy(RedisEvictionPolicy::ALLKEYS_LFU);
    }

    public function setEvictionPolicy(RedisEvictionPolicy $policy): void {
        $this->redis->config('SET', 'maxmemory-policy', $policy->value);
    }

    public function maxCapacity(int $size): void {
        $this->redis->config('SET', 'maxmemory', $size .'');
        $this->redis->config('SET', 'maxmemory-policy', 'allkeys-lru');
    }
    public function get(string $key): ?array
    {
        $data = $this->redis->get($this->prefix . $key);
        return $data ? unserialize($data) : null;
    }

    public function set(string $key, array $data): bool
    {
        $key = $this->prefix . $key;
        $ttl = isset($data['expiry']) ? $data['expiry'] - time() : 0;

        return $ttl > 0
            ? $this->redis->setex($key, $ttl, serialize($data))
            : $this->redis->set($key, serialize($data));
    }

    public function delete(string $key): bool
    {
        return (bool)$this->redis->del($this->prefix . $key);
    }

    public function clear(): bool
    {
        $keys = $this->redis->keys($this->prefix . '*');
        return !$keys || $this->redis->del($keys);
    }
}