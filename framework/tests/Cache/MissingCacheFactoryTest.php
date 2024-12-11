<?php

namespace Tests\Cache;

use DJWeb\Framework\Cache\CacheFactory;
use DJWeb\Framework\Cache\CacheItemPool;
use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\Web\Application;
use Tests\BaseTestCase;

class MissingCacheFactoryTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Application::withInstance(null);
        $app = Application::getInstance();
        $config = $this->createMock(ConfigContract::class);
        $app->set(ConfigContract::class, $config);
        $config->expects($this->any())->method('get')->willReturnCallback(fn($key) => match ($key) {
            'cache.default_driver' => 'unknown',
           default => null,
        });
    }

    public function testCreateCache()
    {
        $this->expectException(\InvalidArgumentException::class);
        $item = CacheFactory::create();
        $this->assertInstanceOf(CacheItemPool::class, $item);
    }
}