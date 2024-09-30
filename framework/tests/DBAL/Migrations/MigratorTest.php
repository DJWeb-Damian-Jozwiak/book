<?php

namespace Tests\DBAL\Migrations;

use DJWeb\Framework\DBAL\Contracts\Migrations\MigrationRepositoryContract;
use DJWeb\Framework\DBAL\Contracts\Migrations\MigrationResolverContract;
use DJWeb\Framework\DBAL\Contracts\Schema\SchemaContract;
use DJWeb\Framework\DBAL\Migrations\MigrationExecutor;
use DJWeb\Framework\DBAL\Migrations\Migrator;
use PHPUnit\Framework\TestCase;

class MigratorTest extends TestCase
{
    private MigrationRepositoryContract $repository;
    private SchemaContract $schema;
    private MigrationResolverContract $resolver;
    private MigrationExecutor $executor;
    private Migrator $migrator;

    public function testRun(): void
    {
        $this->repository->expects($this->once())->method(
            'createMigrationsTable'
        );

        $this->migrator->run();
    }

    public function testRollback(): void
    {
        $this->repository->expects($this->once())
            ->method('getRan')
            ->willReturn(['migration1', 'migration2']);
        $this->resolver->expects($this->once())
            ->method('getMigrationFiles')
            ->willReturn(['migration1', 'migration2', 'migration3']);
        $this->executor->expects($this->once())
            ->method('executeMigrations')
            ->with(['migration2', 'migration1'], 'down')
            ->willReturn(['migration2', 'migration1']);

        $result = $this->migrator->rollback();
        $this->assertEquals(['migration2', 'migration1'], $result);
    }

    protected function setUp(): void
    {
        $this->repository = $this->createMock(
            MigrationRepositoryContract::class
        );
        $this->schema = $this->createMock(SchemaContract::class);
        $this->resolver = $this->createMock(MigrationResolverContract::class);
        $this->executor = $this->createMock(MigrationExecutor::class);

        $this->migrator = new Migrator(
            $this->repository,
            $this->resolver,
            $this->executor
        );
    }
}