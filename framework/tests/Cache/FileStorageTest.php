<?php

declare(strict_types=1);

namespace Tests\Cache;

use Carbon\Carbon;
use DJWeb\Framework\Cache\Storage\FileStorage;
use PHPUnit\Framework\TestCase;

class FileStorageTest extends TestCase
{
    private string $tempDir;
    private FileStorage $storage;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/cache_test_' . uniqid();
        mkdir($this->tempDir);
        $this->storage = new FileStorage($this->tempDir);
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob($this->tempDir . '/*'));
        rmdir($this->tempDir);
    }

    public function testItHandlesLruEviction(): void
    {
        $this->storage->maxCapacity(2);

        $this->storage->set('key1', ['value' => 1]);
        $this->storage->set('key2', ['value' => 2]);
        Carbon::setTestNow(Carbon::now()->addSeconds(2));
        $this->storage->get('key1'); // Access key1 to make it most recently used
        $this->storage->set('key3', ['value' => 3]);
        Carbon::setTestNow(Carbon::now());

        $this->assertNotNull($this->storage->get('key1'));
        $this->assertNull($this->storage->get('key2')); // Should be evicted
        $this->assertNotNull($this->storage->get('key3'));
    }
}