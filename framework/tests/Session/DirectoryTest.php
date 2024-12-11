<?php

namespace Tests\Session;

use DJWeb\Framework\Storage\Directory;
use PHPUnit\Framework\TestCase;

class DirectoryTest extends TestCase
{
    private string $testDir = __DIR__ . '/test_directory';

    protected function setUp(): void
    {
        if (is_dir($this->testDir)) {
            rmdir($this->testDir);
        }
    }

    protected function tearDown(): void
    {
        if (is_dir($this->testDir)) {
            rmdir($this->testDir);
        }
    }

    public function testEnsureDirectoryExistsCreatesDirectory(): void
    {
        $this->assertFalse(is_dir($this->testDir));

        Directory::ensureDirectoryExists($this->testDir);

        $this->assertTrue(is_dir($this->testDir));
        $this->assertTrue(is_writable($this->testDir));
    }

    public function testEnsureDirectoryExistsHandlesExistingDirectory(): void
    {
        mkdir($this->testDir);
        $this->assertTrue(is_dir($this->testDir));

        Directory::ensureDirectoryExists($this->testDir);

        $this->assertTrue(is_dir($this->testDir));
    }

    public function testEnsureDirectoryIsWritableWithWritableDirectory(): void
    {
        mkdir($this->testDir, 0777);

         Directory::ensureDirectoryIsWritable($this->testDir);

        $this->assertTrue(is_writable($this->testDir));
    }

    public function testEnsureDirectoryIsWritableThrowsExceptionForNonWritableDirectory(): void
    {
        mkdir($this->testDir, 0444);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Directory {$this->testDir} is not writable");

        Directory::ensureDirectoryIsWritable($this->testDir);
    }
}