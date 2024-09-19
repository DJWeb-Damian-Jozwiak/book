<?php

namespace Tests\DBAL\Schema\MySQL\Managers;

use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Contracts\Schema\TableManagerContract;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\IntColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\VarcharColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Managers\TableManager;
use DJWeb\Framework\Exceptions\DBAL\Schema\TableError;
use PDOException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TableManagerTest extends TestCase
{
    private TableManagerContract $manager;
    private MockObject $connectionMock;

    public function testCreateTable()
    {
        $columns = [
            new IntColumn('id', nullable: false),
            new VarcharColumn('name', 255)
        ];
        $pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->connectionMock->expects($this->once())
            ->method('query')
            ->with(
                $this->stringContains(
                    'CREATE TABLE users (id INT(11) NOT NULL, name VARCHAR(255) NULL)'
                )
            )
            ->willReturn($pdoStatementMock);

        $this->manager->createTable('users', $columns);
    }

    public function testAlterTable()
    {
        $modifications = [
            new VarcharColumn('email', length: 100),
            ['name', new VarcharColumn('full_name', length: 255)],
            'old_column'
        ];
        $pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->connectionMock->expects($this->once())
            ->method('query')
            ->with(
                $this->stringContains(
                    'ALTER TABLE users ADD COLUMN email VARCHAR(100) NULL, CHANGE COLUMN name full_name VARCHAR(255) NULL, DROP COLUMN old_column'
                )
            )
            ->willReturn($pdoStatementMock);

        $this->manager->alterTable('users', $modifications);
    }

    public function testDropTable()
    {
        $pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->connectionMock->expects($this->once())
            ->method('query')
            ->with('DROP TABLE users')
            ->willReturn($pdoStatementMock);

        $this->manager->dropTable('users');
    }

    public function testAddColumnThrowsExceptionOnDuplicateColumn()
    {
        $column = new IntColumn('id');

        $this->connectionMock->expects($this->once())
            ->method('query')
            ->willThrowException(new PDOException('Table does not exist'));

        $this->expectException(TableError::class);
        $this->expectExceptionMessage(
            'Table does not exist'
        );

        $this->manager->dropTable('users', $column);
    }

    protected function setUp(): void
    {
        $this->connectionMock = $this->createMock(ConnectionContract::class);
        $this->manager = new TableManager($this->connectionMock);
    }
}