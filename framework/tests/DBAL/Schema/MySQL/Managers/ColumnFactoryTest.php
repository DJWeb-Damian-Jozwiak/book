<?php

namespace Tests\DBAL\Schema\MySQL\Managers;

use DJWeb\Framework\DBAL\Contracts\Schema\ColumnFactoryContract;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\DateTimeColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\EnumColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\IntColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\TextColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\VarcharColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Managers\ColumnFactory;
use DJWeb\Framework\Exceptions\DBAL\Schema\UnsupportedColumnType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ColumnFactoryTest extends TestCase
{
    private ColumnFactoryContract $factory;

    public static function columnDescriptionProvider(): array
    {
        return [
            'int column' => [
                [
                    'Field' => 'id',
                    'Type' => 'int(11)',
                    'Null' => 'NO',
                    'Key' => 'PRI',
                    'Default' => null,
                    'Extra' => 'auto_increment'
                ],
                IntColumn::class,
                'id INT(11) NOT NULL AUTO_INCREMENT'
            ],
            'varchar column' => [
                [
                    'Field' => 'name',
                    'Type' => 'varchar(255)',
                    'Null' => 'YES',
                    'Key' => '',
                    'Default' => null,
                    'Extra' => ''
                ],
                VarcharColumn::class,
                'name VARCHAR(255) NULL'
            ],
            'text column' => [
                [
                    'Field' => 'description',
                    'Type' => 'text',
                    'Null' => 'YES',
                    'Key' => '',
                    'Default' => null,
                    'Extra' => ''
                ],
                TextColumn::class,
                'description TEXT NULL'
            ],
            'datetime column' => [
                [
                    'Field' => 'created_at',
                    'Type' => 'datetime',
                    'Null' => 'NO',
                    'Key' => '',
                    'Default' => 'CURRENT_TIMESTAMP',
                    'Extra' => ''
                ],
                DateTimeColumn::class,
                'created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP'
            ],
            'enum column' => [
                [
                    'Field' => 'status',
                    'Type' => "enum('active','inactive','pending')",
                    'Null' => 'NO',
                    'Key' => '',
                    'Default' => 'pending',
                    'Extra' => ''
                ],
                EnumColumn::class,
                "status ENUM('active','inactive','pending') NOT NULL DEFAULT 'pending'"
            ],
            'unsigned int column' => [
                [
                    'Field' => 'age',
                    'Type' => 'int(3) unsigned',
                    'Null' => 'YES',
                    'Key' => '',
                    'Default' => null,
                    'Extra' => ''
                ],
                IntColumn::class,
                'age INT(3) UNSIGNED NULL'
            ],
            'varchar column with default' => [
                [
                    'Field' => 'country',
                    'Type' => 'varchar(50)',
                    'Null' => 'NO',
                    'Key' => '',
                    'Default' => 'USA',
                    'Extra' => ''
                ],
                VarcharColumn::class,
                "country VARCHAR(50) NOT NULL DEFAULT 'USA'"
            ],
        ];
    }

    #[DataProvider('columnDescriptionProvider')]
    public function testCreateColumn(
        array $description,
        string $expectedClass,
        string $expectedSqlDefinition
    ) {
        $column = $this->factory->createFromDescription($description);

        $this->assertInstanceOf($expectedClass, $column);
        $this->assertEquals(
            $expectedSqlDefinition,
            $column->getSqlDefinition()
        );
    }

    public function testThrowsExceptionForUnknownColumnType()
    {
        $this->expectException(UnsupportedColumnType::class);

        $description = [
            'Field' => 'unknown',
            'Type' => 'unknown_type',
            'Null' => 'YES',
            'Key' => '',
            'Default' => null,
            'Extra' => ''
        ];

        $this->factory->createFromDescription($description);
    }

    protected function setUp(): void
    {
        $this->factory = new ColumnFactory();
    }

}