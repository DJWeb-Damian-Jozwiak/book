<?php

namespace Tests\DBAL\Migrations;

use DJWeb\Framework\DBAL\Contracts\Migrations\MigrationRepositoryContract;
use DJWeb\Framework\DBAL\Contracts\Schema\SchemaContract;
use DJWeb\Framework\DBAL\Migrations\Migration;
use DJWeb\Framework\DBAL\Migrations\MigrationExecutor;
use DJWeb\Framework\DBAL\Migrations\MigrationResolver;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MigrationExecutorTest extends TestCase
{
    private SchemaContract $schema;
    private MigrationRepositoryContract $repository;
    private MigrationResolver $resolver;
    private MigrationExecutor $executor;

    public static function migrationProvider(): array
    {
        return [
            'up' => ['up'],
            'down' => ['down'],
        ];
    }

    #[DataProvider('migrationProvider')]
    public function testExecuteMigrations(string $method): void
    {
        $migration = $this->createMock(Migration::class);
        $migration->expects($this->once())->method('withSchema')->with(
            $this->schema
        );
        $migration->expects($this->once())->method($method);

        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with('migration1')
            ->willReturn($migration);

        $result = $this->executor->executeMigrations(['migration1'],
            $method,
            false);
        $this->assertEquals(['migration1'], $result);
    }

    protected function setUp(): void
    {
        $this->schema = $this->createMock(SchemaContract::class);
        $this->repository = $this->createMock(
            MigrationRepositoryContract::class
        );
        $this->resolver = $this->createMock(MigrationResolver::class);

        $this->executor = new MigrationExecutor(
            $this->schema,
            $this->repository,
            $this->resolver
        );
    }
}