<?php

namespace Tests\DBAL\Schema\MySQL;

use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Contracts\Schema\ColumnManagerContract;
use DJWeb\Framework\DBAL\Contracts\Schema\DatabaseInfoContract;
use DJWeb\Framework\DBAL\Contracts\Schema\IndexManagerContract;
use DJWeb\Framework\DBAL\Contracts\Schema\SchemaContract;
use DJWeb\Framework\DBAL\Contracts\Schema\TableManagerContract;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\IntColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\VarcharColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Managers\ColumnFactory;
use DJWeb\Framework\DBAL\Schema\MySQL\Schema;
use DJWeb\Framework\DBAL\Schema\MySQL\Transaction;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SchemaTest extends TestCase
{
    private SchemaContract $schema;
    private MockObject $tableManagerMock;
    private MockObject $columnManagerMock;
    private MockObject $indexManagerMock;
    private MockObject $databaseInfoMock;

    public function testCreateTable()
    {
        $columns = [new IntColumn('id')];
        $this->tableManagerMock->expects($this->once())->method(
            'createTable'
        )->with('users', $columns);
        $this->schema->createTable('users', $columns);
    }

    public function testCreateFromDescription()
    {
        $description = [
            'Field' => 'id',
            'Type' => 'int(11)',
            'Null' => 'NO',
            'Key' => 'PRI',
            'Default' => null,
            'Extra' => 'auto_increment'
        ];
        $expectedProperties = [
            'name' => 'id',
            'length' => 11,
            'nullable' => false,
            'autoIncrement' => true
        ];
        $column = $this->schema->createFromDescription($description);

        $this->assertInstanceOf(IntColumn::class, $column);

        foreach ($expectedProperties as $property => $value) {
            $this->assertEquals($value, $column->$property);
        }
    }

    public function testAlterTable()
    {
        $modifications = [new VarcharColumn('email')];
        $this->tableManagerMock->expects($this->once())->method(
            'alterTable'
        )->with('users', $modifications);
        $this->schema->alterTable('users', $modifications);
    }

    public function testDropTable()
    {
        $this->tableManagerMock->expects($this->once())->method(
            'dropTable'
        )->with('users');
        $this->schema->dropTable('users');
    }

    public function testAddColumn()
    {
        $column = new IntColumn('age');
        $this->columnManagerMock->expects($this->once())->method(
            'addColumn'
        )->with('users', $column);
        $this->schema->addColumn('users', $column);
    }


    public function testModifyColumn()
    {
        $newColumn = new VarcharColumn('full_name');
        $this->columnManagerMock->expects($this->once())->method(
            'modifyColumn'
        )->with('users', 'name', $newColumn);
        $this->schema->modifyColumn('users', 'name', $newColumn);
    }

    public function testDropColumn()
    {
        $this->columnManagerMock->expects($this->once())->method(
            'dropColumn'
        )->with('users', 'age');
        $this->schema->dropColumn('users', 'age');
    }

    public function testCreateIndex()
    {
        $this->indexManagerMock->expects($this->once())->method(
            'createIndex'
        )->with('users', 'idx_name', ['name']);
        $this->schema->createIndex('users', 'idx_name', ['name']);
    }

    public function testCreatePrimaryIndex()
    {
        $this->indexManagerMock->expects($this->once())->method(
            'primary'
        )->with('users', ['id']);
        $this->schema->primaryIndex('users', ['id']);
    }

    public function testCreateUniqueIndex()
    {
        $this->indexManagerMock->expects($this->once())->method(
            'unique'
        )->with('users', 'idx_name', ['name']);
        $this->schema->uniqueIndex('users', 'idx_name', ['name']);
    }

    public function testDropIndex()
    {
        $this->indexManagerMock->expects($this->once())->method(
            'dropIndex'
        )->with('users', 'idx_name');
        $this->schema->dropIndex('users', 'idx_name');
    }


    public function testGetTables()
    {
        $expectedTables = ['users', 'products'];
        $this->databaseInfoMock->expects($this->once())->method(
            'getTables'
        )->willReturn($expectedTables);
        $this->assertEquals($expectedTables, $this->schema->getTables());
    }

    public function testGetColumns()
    {
        $expectedColumns = [['Field' => 'id', 'Type' => 'int(11)']];
        $this->databaseInfoMock->expects($this->once())->method(
            'getColumns'
        )->with('users')->willReturn($expectedColumns);
        $this->assertEquals(
            $expectedColumns,
            $this->schema->getColumns('users')
        );
    }

    public function testGetTransaction()
    {
        $transaction = $this->schema->getTransaction();
        $this->assertInstanceOf(Transaction::class, $transaction);
    }

    public function testGetConnection()
    {
        $connection = $this->schema->getConnection();
        $this->assertInstanceOf(ConnectionContract::class, $connection);
    }

    protected function setUp(): void
    {
        $this->tableManagerMock = $this->createMock(
            TableManagerContract::class
        );
        $this->columnManagerMock = $this->createMock(
            ColumnManagerContract::class
        );
        $this->indexManagerMock = $this->createMock(
            IndexManagerContract::class
        );
        $this->databaseInfoMock = $this->createMock(
            DatabaseInfoContract::class
        );

        $connectionMock = $this->createMock(ConnectionContract::class);
        $this->schema = new Schema(
            $this->tableManagerMock,
            $this->columnManagerMock,
            $this->indexManagerMock,
            $this->databaseInfoMock,
            new ColumnFactory(),
            new Transaction($this->createMock(ConnectionContract::class)),
            $connectionMock
        );
    }
}