<?php

namespace Tests\Cache;

use DJWeb\Framework\Cache\CacheFactory;
use DJWeb\Framework\Cache\CacheItemPool;
use DJWeb\Framework\Cache\RedisEvictionPolicy;
use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\Web\Application;
use Redis;
use Tests\BaseTestCase;

class RedisCacheFactoryTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Application::withInstance(null);
        $app = Application::getInstance();
        $config = $this->createMock(ConfigContract::class);
        $app->set(ConfigContract::class, $config);
        $config->expects($this->any())->method('get')->willReturnCallback(fn($key) => match ($key) {
            'cache.default_driver' => 'redis',
            'cache.stores.redis' => [
                'driver' => 'redis',
                'connection' => 'default',
                'password' => 'password',
                'database' => 1,
                'eviction_policy' => RedisEvictionPolicy::ALLKEYS_LRU,
                'max_memory' => 100,
            ],
            'cache.prefix' => 'test_prefix',

        });
    }

    public function testCreateCache()
    {
        $redis = $this->createMock(Redis::class);
        $redis->expects($this->once())
            ->method('connect')
            ->with(
                'localhost',
                6379,
                0.0
            )
            ->willReturn(true);
        $redis->expects($this->once())->method('auth')->with('password');
        CacheFactory::withRedis($redis);
        $item = CacheFactory::create();
        $this->assertInstanceOf(CacheItemPool::class, $item);
    }
}