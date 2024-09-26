<?php

namespace Tests\DBAL\Schema\MySQL\Managers;

use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Contracts\Schema\DatabaseInfoContract;
use DJWeb\Framework\DBAL\Schema\Column;
use DJWeb\Framework\DBAL\Schema\MySQL\Managers\DatabaseInfo;
use DJWeb\Framework\Exceptions\DBAL\Schema\SchemaError;
use PDOException;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DatabaseInfoTest extends TestCase
{
    private DatabaseInfoContract $databaseInfo;
    private MockObject $connectionMock;

    public function testGetTables()
    {
        $expectedTables = ['users', 'products', 'orders'];

        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method('fetchAll')->willReturn($expectedTables);

        $this->connectionMock->expects($this->once())
            ->method('query')
            ->with('SHOW TABLES')
            ->willReturn($stmtMock);

        $tables = $this->databaseInfo->getTables();
        $this->assertEquals($expectedTables, $tables);
    }

    public function testGetColumns()
    {
        $expectedColumns = [
            [
                'Field' => 'id',
                'Type' => 'int(11)',
                'Null' => 'NO',
                'Key' => 'PRI',
                'Default' => null,
                'Extra' => 'auto_increment'
            ],
            [
                'Field' => 'name',
                'Type' => 'varchar(255)',
                'Null' => 'YES',
                'Key' => '',
                'Default' => null,
                'Extra' => ''
            ]
        ];

        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method('fetchAll')->willReturn($expectedColumns);

        $this->connectionMock->expects($this->once())
            ->method('query')
            ->with('DESCRIBE `users`')
            ->willReturn($stmtMock);

        $columns = $this->databaseInfo->getColumns('users');
        $this->assertInstanceOf(Column::class, $columns[0]);
    }

    public function testGetColumnsThrowsExceptionOnPDOError()
    {
        $this->connectionMock->expects($this->once())
            ->method('query')
            ->with('DESCRIBE `users`')
            ->willThrowException(new PDOException('Table does not exist'));
        $this->expectException(SchemaError::class);

        $this->databaseInfo->getColumns('users');
    }

    public function testGetColumnsThrowsExceptionOnEmptyTableName()
    {
        $this->expectException(SchemaError::class);

        $this->databaseInfo->getColumns('');
    }


    protected function setUp(): void
    {
        $this->connectionMock = $this->createMock(ConnectionContract::class);
        $this->databaseInfo = new DatabaseInfo($this->connectionMock);
    }
}