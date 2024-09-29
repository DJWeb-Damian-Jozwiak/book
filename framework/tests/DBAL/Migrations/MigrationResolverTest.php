<?php

namespace Tests\DBAL\Migrations;

use DJWeb\Framework\Base\Application;
use DJWeb\Framework\DBAL\Migrations\Migration;
use DJWeb\Framework\DBAL\Migrations\MigrationResolver;
use Tests\BaseTestCase;

class MigrationResolverTest extends BaseTestCase
{
    private $resolver;
    private $tempDir;
    private $tempDir2;

    public function tearDown(): void
    {
        array_map('unlink', glob("$this->tempDir/*.*"));
        rmdir($this->tempDir);
        parent::tearDown();
    }
    public function testResolveTestMigration()
    {
        $this->tempDir2 = dirname(
                __DIR__,
                3
            ) . '/tests/migrations';
        $this->resolver = new MigrationResolver($this->tempDir2);
        $migration = $this->resolver->resolve('2024_01_01_000001_create_users');
        $this->assertInstanceOf(Migration::class, $migration);
    }

    public function testGetMigrationFiles(): void
    {
        file_put_contents(
            $this->tempDir . '/2024_01_01_000001_create_users.php',
            '<?php
            return new class {};
            '
        );
        file_put_contents(
            $this->tempDir . '/2024_01_02_000002_add_email_to_users.php',
            <<<MSG
<?php
return new class {};
MSG

        );

        $result = $this->resolver->getMigrationFiles();
        $this->assertEquals([
            '2024_01_01_000001_create_users',
            '2024_01_02_000002_add_email_to_users'
        ], $result);
    }

    public function testResolve()
    {
        file_put_contents(
            $this->tempDir . '/2024_01_02_000002_add_email_to_users.php',
            <<<MSG
<?php
return new class {};
MSG
        );
        $this->expectException(\RuntimeException::class);
        $this->resolver->resolve(
            '2024_01_02_000002_add_email_to_users'
        );
    }

    public function testNotExistingDirectory()
    {
        $app = Application::getInstance();
        $app->bind('app.migrations_path', 'NonExistingDirectory');
        $this->resolver = new MigrationResolver('NonExistingDirectory');
        $this->expectException(\RuntimeException::class);
        $this->resolver->getMigrationFiles();
    }

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/migrations_' . uniqid();
        mkdir($this->tempDir);
        $app = Application::getInstance();
        $app->bind('app.migrations_path', $this->tempDir);
        $this->resolver = new MigrationResolver($this->tempDir);
    }
}