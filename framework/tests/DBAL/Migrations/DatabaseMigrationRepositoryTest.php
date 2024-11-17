<?php

namespace Tests\DBAL\Migrations;

use DJWeb\Framework\Base\Application;
use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Contracts\Query\DeleteQueryBuilderContract;
use DJWeb\Framework\DBAL\Contracts\Query\InsertQueryBuilderContract;
use DJWeb\Framework\DBAL\Contracts\Query\SelectQueryBuilderContract;
use DJWeb\Framework\DBAL\Contracts\Query\UpdateQueryBuilderContract;
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
            InsertQueryBuilderContract::class
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
            DeleteQueryBuilderContract::class
        );
        $deleteBuilder->expects($this->once())
            ->method('table')
            ->with('migrations');

        $this->repository->delete('new_migration');
    }

    public function testRan(): void
    {
        $selectBuilder = Application::getInstance()->get(
            SelectQueryBuilderContract::class
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
            InsertQueryBuilderContract::class,
            $this->createMock(InsertQueryBuilder::class)
        );
        Application::getInstance()->set(
            UpdateQueryBuilderContract::class,
            $this->createMock(UpdateQueryBuilder::class)
        );
        Application::getInstance()->set(
            DeleteQueryBuilderContract::class,
            $this->createMock(DeleteQueryBuilder::class)
        );
        Application::getInstance()->set(
            SelectQueryBuilderContract::class,
            $this->createMock(SelectQueryBuilder::class)
        );
        Application::getInstance()->set(
            Schema::class,
            $this->createMock(Schema::class)
        );
        $this->queryBuilder = $this->createMock(QueryBuilder::class);
        $this->repository = new DatabaseMigrationRepository();
    }
}