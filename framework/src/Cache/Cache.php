<?php

declare(strict_types=1);

namespace DJWeb\Framework\Cache;

use DateInterval;
use DJWeb\Framework\Config\Config;
use Psr\Cache\CacheItemPoolInterface;

class Cache
{
    private static ?CacheItemPoolInterface $pool = null;

    public static function init(CacheItemPoolInterface $pool): void
    {
        self::$pool = $pool;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $item = self::$pool->getItem($key);
        return $item->isHit() ? $item->get() : $default;
    }

    public static function put(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        $item = self::$pool->getItem($key);
        return self::$pool->save(
            $item->set($value)->expiresAfter($ttl)
        );
    }

    public static function forget(string $key): bool
    {
        Config::get('cache.default_driver');
        return self::$pool->deleteItem($key);
    }

    public static function has(string $key): bool
    {
        return self::$pool->hasItem($key);
    }

    public static function remember(string $key, DateInterval|int $ttl, callable $callback): mixed
    {
        if (self::has($key)) {
            return self::get($key);
        }

        $value = $callback();
        self::put($key, $value, $ttl);
        return $value;
    }
}