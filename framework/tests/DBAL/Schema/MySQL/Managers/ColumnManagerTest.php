<?php

namespace Tests\DBAL\Schema\MySQL\Managers;

use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Contracts\Schema\ColumnManagerContract;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\IntColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Managers\ColumnManager;
use DJWeb\Framework\Exceptions\DBAL\Schema\ColumnError;
use PDOException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ColumnManagerTest extends TestCase
{
    private ColumnManagerContract $manager;
    private MockObject $connectionMock;

    public function testAddColumn()
    {
        $column = new IntColumn('new_column');

        // Tworzymy mock dla PDOStatement
        $pdoStatementMock = $this->createMock(\PDOStatement::class);

        $this->connectionMock->expects($this->once())
            ->method('query')
            ->with(
                $this->stringContains(
                    'ALTER TABLE users ADD COLUMN new_column INT(11) NULL'
                )
            )
            ->willReturn($pdoStatementMock);

        $this->manager->addColumn('users', $column);
    }


    public function testAddColumnThrowsExceptionOnDuplicateColumn()
    {
        $column = new IntColumn('id');

        $this->connectionMock->expects($this->once())
            ->method('query')
            ->willThrowException(new PDOException('Duplicate column name'));

        $this->expectException(ColumnError::class);
        $this->expectExceptionMessage(
            'Failed to add column: Duplicate column name'
        );

        $this->manager->addColumn('users', $column);
    }


    public function testModifyColumn()
    {
        $newColumn = new IntColumn('modified_column', length: 100);

        $pdoStatementMock = $this->createMock(\PDOStatement::class);

        $this->connectionMock->expects($this->once())
            ->method('query')
            ->with(
                $this->stringContains(
                    'ALTER TABLE users CHANGE COLUMN old_column modified_column INT(100) NULL',
                    false
                )
            )
            ->willReturn($pdoStatementMock);

        $this->manager->modifyColumn('users', 'old_column', $newColumn);
    }


    public function testDropColumn()
    {
        $pdoStatementMock = $this->createMock(\PDOStatement::class);

        $this->connectionMock->expects($this->once())
            ->method('query')
            ->with(
                $this->stringContains(
                    'ALTER TABLE users DROP COLUMN column_to_drop'
                )
            )
            ->willReturn($pdoStatementMock);

        $this->manager->dropColumn('users', 'column_to_drop');
    }

    protected function setUp(): void
    {
        $this->connectionMock = $this->createMock(ConnectionContract::class);
        $this->manager = new ColumnManager($this->connectionMock);
    }
}