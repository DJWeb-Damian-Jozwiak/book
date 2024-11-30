<?php

declare(strict_types=1);

namespace Tests\Cache;

use Carbon\Carbon;
use DJWeb\Framework\Cache\CacheItemPool;
use DJWeb\Framework\Cache\Storage\FileStorage;
use PHPUnit\Framework\TestCase;

class CacheItemPoolTest extends TestCase
{
    private CacheItemPool $pool;
    private FileStorage $storage;

    protected function setUp(): void
    {
        $tempDir = sys_get_temp_dir() . '/cache_test_' . uniqid();
        mkdir($tempDir);
        $this->storage = new FileStorage($tempDir);
        $this->pool = new CacheItemPool($this->storage);
    }

    public function testItHandlesTtl(): void
    {
        $item = $this->pool->getItem('key')
            ->set('value')
            ->expiresAfter(1);

        $this->pool->save($item);
        $this->assertTrue($this->pool->hasItem('key'));

        Carbon::setTestNow(Carbon::now()->addSeconds(2));
        $this->assertFalse($this->pool->hasItem('key'));
    }

    public function testItHandlesDeferredItems(): void
    {
        $item = $this->pool->getItem('key')->set('value');
        $this->pool->saveDeferred($item);

        $this->assertFalse($this->pool->hasItem('key'));
        $this->pool->commit();
        $this->assertTrue($this->pool->hasItem('key'));
    }
}