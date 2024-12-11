<?php

namespace Tests\Cache;

use DateInterval;
use DJWeb\Framework\Cache\Cache;
use DJWeb\Framework\Web\Application;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Tests\BaseTestCase;

class CacheTest extends BaseTestCase
{
    private CacheItemPoolInterface $pool;

    protected function setUp(): void
    {
        parent::setUp();
        Application::withInstance(null);
        $app = Application::getInstance();
        $app->bind('base_path', dirname(__DIR__));
        $this->pool = $this->createMock(CacheItemPoolInterface::class);
        Cache::init($this->pool);
    }

    public function testGet(): void
    {
        $item = $this->createMock(CacheItemInterface::class);
        $item->method('isHit')->willReturn(true);
        $item->method('get')->willReturn('cached value');

        $this->pool->expects($this->once())
            ->method('getItem')
            ->with('test-key')
            ->willReturn($item);

        $result = Cache::get('test-key');
        $this->assertEquals('cached value', $result);
    }

    public function testGetReturnsDefaultWhenMiss(): void
    {
        $item = $this->createMock(CacheItemInterface::class);
        $item->method('isHit')->willReturn(false);

        $this->pool->expects($this->once())
            ->method('getItem')
            ->with('test-key')
            ->willReturn($item);

        $result = Cache::get('test-key', 'default');
        $this->assertEquals('default', $result);
    }

    public function testPut(): void
    {
        $item = $this->createMock(CacheItemInterface::class);
        $item->expects($this->once())
            ->method('set')
            ->with('test value')
            ->willReturnSelf();

        $item->expects($this->once())
            ->method('expiresAfter')
            ->with(60)
            ->willReturnSelf();

        $this->pool->method('getItem')
            ->with('test-key')
            ->willReturn($item);

        $this->pool->expects($this->once())
            ->method('save')
            ->with($item)
            ->willReturn(true);

        $result = Cache::put('test-key', 'test value', 60);
        $this->assertTrue($result);
    }

    public function testForget(): void
    {
        $this->pool->expects($this->once())
            ->method('deleteItem')
            ->with('test-key')
            ->willReturn(true);

        $result = Cache::forget('test-key');
        $this->assertTrue($result);
    }

    public function testHas(): void
    {
        $this->pool->expects($this->once())
            ->method('hasItem')
            ->with('test-key')
            ->willReturn(true);

        $result = Cache::has('test-key');
        $this->assertTrue($result);
    }

    public function testRememberReturnsExistingValue(): void
    {
        $this->pool->method('hasItem')
            ->with('test-key')
            ->willReturn(true);

        $item = $this->createMock(CacheItemInterface::class);
        $item->method('isHit')->willReturn(true);
        $item->method('get')->willReturn('cached value');

        $this->pool->method('getItem')
            ->with('test-key')
            ->willReturn($item);

        $callbackCalled = false;
        $callback = function() use (&$callbackCalled) {
            $callbackCalled = true;
            return 'new value';
        };

        $result = Cache::remember('test-key', 60, $callback);

        $this->assertEquals('cached value', $result);
        $this->assertFalse($callbackCalled, 'Callback should not have been called');
    }

    public function testRememberStoresNewValue(): void
    {
        $this->pool->method('hasItem')
            ->with('test-key')
            ->willReturn(false);

        $item = $this->createMock(CacheItemInterface::class);
        $item->expects($this->once())
            ->method('set')
            ->with('new value')
            ->willReturnSelf();

        $item->expects($this->once())
            ->method('expiresAfter')
            ->with(60)
            ->willReturnSelf();

        $this->pool->method('getItem')
            ->with('test-key')
            ->willReturn($item);

        $this->pool->expects($this->once())
            ->method('save')
            ->with($item)
            ->willReturn(true);

        $result = Cache::remember('test-key', 60, fn() => 'new value');
        $this->assertEquals('new value', $result);
    }

    public function testRememberWithDateInterval(): void
    {
        $interval = new DateInterval('PT1H'); // 1 hour

        $this->pool->method('hasItem')
            ->with('test-key')
            ->willReturn(false);

        $item = $this->createMock(CacheItemInterface::class);
        $item->expects($this->once())
            ->method('set')
            ->with('new value')
            ->willReturnSelf();

        $item->expects($this->once())
            ->method('expiresAfter')
            ->with($interval)
            ->willReturnSelf();

        $this->pool->method('getItem')
            ->with('test-key')
            ->willReturn($item);

        $this->pool->expects($this->once())
            ->method('save')
            ->with($item)
            ->willReturn(true);

        $result = Cache::remember('test-key', $interval, fn() => 'new value');
        $this->assertEquals('new value', $result);
    }
}