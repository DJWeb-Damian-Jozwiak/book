<?php

namespace Tests\Cache;

use DJWeb\Framework\Cache\RedisEvictionPolicy;
use DJWeb\Framework\Cache\Storage\RedisStorage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Redis;

class RedisStorageTest extends TestCase
{
    private MockObject $redisMock;
    private RedisStorage $storage;

    protected function setUp(): void
    {
        $this->redisMock = $this->createMock(Redis::class);
        $this->storage = new RedisStorage($this->redisMock);
    }

    /** @test */
    public function it_configures_eviction_policy(): void
    {
        $this->redisMock->expects($this->exactly(2))
            ->method('config');

        $this->redisMock->method('config')
            ->willReturnMap([
                ['SET', 'maxmemory', 1024, true],
                ['SET', 'maxmemory-policy', RedisEvictionPolicy::ALLKEYS_LRU->value, true]
            ]);

        $this->storage->maxCapacity(1024);
    }
}