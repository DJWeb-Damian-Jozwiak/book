<?php

namespace Tests\DBAL\Schema\MySQL\Managers;

use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Contracts\Schema\IndexManagerContract;
use DJWeb\Framework\DBAL\Schema\MySQL\Managers\IndexManager;
use DJWeb\Framework\Exceptions\DBAL\Schema\IndexError;
use PDOException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class IndexManagerTest extends TestCase
{
    private IndexManagerContract $manager;
    private MockObject $connectionMock;

    public function testCreateIndex()
    {
        $pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->connectionMock->expects($this->once())
            ->method('query')
            ->with(
                $this->stringContains(
                    'CREATE INDEX idx_name ON users (name, email)'
                )
            )
            ->willReturn($pdoStatementMock);

        $this->manager->createIndex('users', 'idx_name', ['name', 'email']);
    }

    public function testDropIndex()
    {
        $pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->connectionMock->expects($this->once())
            ->method('query')
            ->with($this->stringContains('DROP INDEX idx_name ON users'))
            ->willReturn($pdoStatementMock);

        $this->manager->dropIndex('users', 'idx_name');
    }

    public function testCreatePrimaryKey()
    {
        $pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->connectionMock->expects($this->once())
            ->method('query')
            ->with(
                $this->stringContains('ALTER TABLE users ADD PRIMARY KEY (id)')
            )
            ->willReturn($pdoStatementMock);

        $this->manager->primary('users', ['id']);
    }

    public function testCreateUniqueIndex()
    {
        $pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->connectionMock->expects($this->once())
            ->method('query')
            ->with(
                $this->stringContains(
                    'CREATE UNIQUE INDEX idx_unique_email ON users (email)'
                )
            )
            ->willReturn($pdoStatementMock);

        $this->manager->unique('users', 'idx_unique_email', ['email']);
    }

    public function testCreateIndexThrowsExceptionOnDuplicateIndex()
    {
        $this->connectionMock->expects($this->once())
            ->method('query')
            ->willThrowException(new PDOException('Duplicate key name'));

        $this->expectException(IndexError::class);
        $this->expectExceptionMessage(
            'Failed to create index: Duplicate key name'
        );

        $this->manager->createIndex('users', 'idx_email', ['email']);
    }

    protected function setUp(): void
    {
        $this->connectionMock = $this->createMock(ConnectionContract::class);
        $this->manager = new IndexManager($this->connectionMock);
    }
}