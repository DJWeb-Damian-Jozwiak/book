<?php

declare(strict_types=1);

namespace Tests\Session;

use DJWeb\Framework\ErrorHandling\Handlers\WebHandler;
use DJWeb\Framework\ErrorHandling\Renderers\WebRenderer;
use DJWeb\Framework\Storage\File;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    private string $testDir;
    private string $testFile;

    private WebRenderer|MockObject $renderer;
    private array $output;
    private WebHandler $handler;

    protected function setUp(): void
    {
        $this->testDir = __DIR__ . '/test_files';
        $this->testFile = $this->testDir . '/test.txt';

        if (!is_dir($this->testDir)) {
            mkdir($this->testDir, 0777, true);
        }
    }

    protected function tearDown(): void
    {
        if (is_file($this->testFile)) {
            unlink($this->testFile);
        }
        if (is_dir($this->testDir)) {
            rmdir($this->testDir);
        }
    }

    public function testCreateCreatesNewFileWithContent(): void
    {
        $content = 'Test content';

        File::create($this->testFile, $content);

        $this->assertTrue(file_exists($this->testFile));
        $this->assertEquals($content, file_get_contents($this->testFile));
        $this->assertTrue(is_writable($this->testFile));
    }

    public function testCreateSetsCorrectPermissions(): void
    {
        $mode = 0444;

        File::create($this->testFile, 'content', $mode);

        $this->assertEquals($mode, fileperms($this->testFile) & 0777);
    }

    public function testEnsureFileExistsWithExistingFile(): void
    {
        file_put_contents($this->testFile, 'test content');

        //should not throw exception
        File::ensureFileExists($this->testFile);

        $this->assertTrue(true); // Test przeszedł, jeśli dotarliśmy do tego miejsca
    }

    public function testEnsureFileExistsThrowsExceptionForNonExistentFile(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Unable to read file: {$this->testFile}");

        File::ensureFileExists($this->testFile);
    }

    public function testUnlinkRemovesExistingFile(): void
    {
        file_put_contents($this->testFile, 'test content');
        $this->assertTrue(file_exists($this->testFile));

        File::unlink($this->testFile);

        $this->assertFalse(file_exists($this->testFile));
    }

    public function testUnlinkHandlesNonExistentFile(): void
    {
        //should not throw exception
        File::unlink($this->testFile);

        $this->assertTrue(true);
    }

    public function testEnsureFileIsReadableWithReadableFile(): void
    {
        file_put_contents($this->testFile, 'test content');
        chmod($this->testFile, 0444); // ustawienie praw tylko do odczytu

        //should not throw exception
        File::ensureFileIsReadable($this->testFile);

        $this->assertTrue(true);
    }

    public function testEnsureFileIsReadableThrowsExceptionForNonReadableFile(): void
    {
        file_put_contents($this->testFile, 'test content');
        chmod($this->testFile, 0000); // usunięcie wszystkich uprawnień

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Unable to read file: {$this->testFile}");

        File::ensureFileIsReadable($this->testFile);
    }
}
