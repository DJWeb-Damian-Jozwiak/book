<?php

declare(strict_types=1);

namespace DJWeb\Framework\Cache;

enum RedisEvictionPolicy: string
{
    case ALLKEYS_LRU = 'allkeys-lru';
    case VOLATILE_LRU = 'volatile-lru';
    case ALLKEYS_LFU = 'allkeys-lfu';
    case VOLATILE_LFU = 'volatile-lfu';
    case ALLKEYS_RANDOM = 'allkeys-random';
    case VOLATILE_RANDOM = 'volatile-random';
    case VOLATILE_TTL = 'volatile-ttl';
    case NO_EVICTION = 'noeviction';

    public function description(): string {
        return match($this) {
            self::ALLKEYS_LRU => 'Evict using approximated LRU among all keys',
            self::VOLATILE_LRU => 'Evict using approximated LRU among keys with expiration',
            self::ALLKEYS_LFU => 'Evict using approximated LFU among all keys',
            self::VOLATILE_LFU => 'Evict using approximated LFU among keys with expiration',
            self::ALLKEYS_RANDOM => 'Evict random keys among all keys',
            self::VOLATILE_RANDOM => 'Evict random keys among keys with expiration',
            self::VOLATILE_TTL => 'Evict keys with shortest TTL among keys with expiration',
            self::NO_EVICTION => 'Return error when memory limit reached',
        };
    }
}