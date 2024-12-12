<?php

namespace Tests\Cache;

use DJWeb\Framework\Cache\CacheFactory;
use DJWeb\Framework\Cache\CacheItemPool;
use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\Web\Application;
use Tests\BaseTestCase;

class FileCacheFactoryTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Application::withInstance(null);
        $app = Application::getInstance();
        $config = $this->createMock(ConfigContract::class);
        $app->set(ConfigContract::class, $config);
        $config->expects($this->any())->method('get')->willReturnCallback(fn($key) => match ($key) {
            'cache.default_driver' => 'file',
            'cache.stores.file' => [
                'driver' => 'file',
                'path' => __DIR__ . '/../../storage/cache/file',
            ]
        });
    }

    public function testCreateCache()
    {
        $item = CacheFactory::create();
        $this->assertInstanceOf(CacheItemPool::class, $item);
    }
}