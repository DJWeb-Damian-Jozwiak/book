<?php

namespace Tests\DBAL\Migrations;

use DJWeb\Framework\Base\Application;
use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Migrations\DatabaseMigrationRepository;
use DJWeb\Framework\DBAL\Query\Builders\DeleteQueryBuilder;
use DJWeb\Framework\DBAL\Query\Builders\InsertQueryBuilder;
use DJWeb\Framework\DBAL\Query\Builders\QueryBuilder;
use DJWeb\Framework\DBAL\Query\Builders\SelectQueryBuilder;
use DJWeb\Framework\DBAL\Query\Builders\UpdateQueryBuilder;
use DJWeb\Framework\DBAL\Schema\MySQL\Schema;
use Tests\BaseTestCase;

class DatabaseMigrationRepositoryTest extends BaseTestCase
{
    private ConnectionContract $mockConnection;
    private QueryBuilder $queryBuilder;
    private DatabaseMigrationRepository $repository;


    public function testLog(): void
    {
        $insertBuilder = Application::getInstance()->get(
            InsertQueryBuilder::class
        );
        $insertBuilder->expects($this->once())
            ->method('table')
            ->with('migrations');


        $this->repository->log('new_migration');
    }

    public function testCreateMigrationsTable()
    {
        $schema = Application::getInstance()->get(Schema::class);
        $schema->expects($this->once())->method('getTables')->willReturn([]);
        $schema->expects($this->once())->method('createTable');
        $this->repository->createMigrationsTable();
    }

    public function testDelete(): void
    {
        $deleteBuilder = Application::getInstance()->get(
            DeleteQueryBuilder::class
        );
        $deleteBuilder->expects($this->once())
            ->method('table')
            ->with('migrations');

        $this->repository->delete('new_migration');
    }

    public function testRan(): void
    {
        $selectBuilder = Application::getInstance()->get(
            SelectQueryBuilder::class
        );
        $selectBuilder->expects($this->once())->method('table')->with(
            'migrations'
        );

        $this->repository->getRan();
    }


    protected function setUp(): void
    {
        $this->mockConnection = $this->createMock(ConnectionContract::class);
        Application::getInstance()->set(
            InsertQueryBuilder::class,
            $this->createMock(InsertQueryBuilder::class)
        );
        Application::getInstance()->set(
            UpdateQueryBuilder::class,
            $this->createMock(UpdateQueryBuilder::class)
        );
        Application::getInstance()->set(
            DeleteQueryBuilder::class,
            $this->createMock(DeleteQueryBuilder::class)
        );
        Application::getInstance()->set(
            SelectQueryBuilder::class,
            $this->createMock(SelectQueryBuilder::class)
        );
        Application::getInstance()->set(
            Schema::class,
            $this->createMock(Schema::class)
        );
        $this->queryBuilder = $this->createMock(QueryBuilder::class);
        $this->repository = new DatabaseMigrationRepository(
            $this->mockConnection
        );
    }
}