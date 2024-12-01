<?php

declare(strict_types=1);

namespace DJWeb\Framework\Cache;

use DJWeb\Framework\Cache\Storage\FileStorage;
use DJWeb\Framework\Cache\Storage\RedisStorage;
use DJWeb\Framework\Config\Config;
use InvalidArgumentException;
use Redis;

class CacheFactory
{
    public static function create(): CacheItemPool
    {
        $driver = Config::get('cache.default_driver');
        $config = Config::get("cache.stores.{$driver}");

        $storage = match ($driver) {
            'file' => self::createFileStorage($config),
            'redis' => self::createRedisStorage($config),
            default => throw new InvalidArgumentException("Unsupported cache driver: {$driver}")
        };

        return new CacheItemPool($storage);
    }

    private static function createFileStorage(array $config): FileStorage
    {
        $storage = new FileStorage($config['path']);
        $storage->maxCapacity($config['max_items'] ?? 1000);
        return $storage;
    }

    private static function createRedisStorage(array $config): RedisStorage
    {
        $redis = new Redis();
        $redis->connect(
            $config['host'] ?? 'localhost',
            $config['port'] ?? 6379,
            $config['timeout'] ?? 0.0,
        );

        if (isset($config['password'])) {
            $redis->auth($config['password']);
        }

        if (isset($config['database'])) {
            $redis->select($config['database']);
        }

        $storage = new RedisStorage($redis, $config['prefix'] ?? 'cache:');
        if (isset($config['max_memory'])) {
            $storage->maxCapacity($config['max_memory']);
        }
        if (isset($config['eviction_policy'])) {
            $storage->setEvictionPolicy($config['eviction_policy']);
        }
        return $storage;
    }
}
