<?php

namespace Tests\DBAL\Schema\MySQL\Columns;

use DJWeb\Framework\DBAL\Schema\MySQL\Columns\EnumColumn;
use DJWeb\Framework\Exceptions\DBAL\Schema\UnsupportedColumnType;
use PHPUnit\Framework\TestCase;

class EnumColumnTest extends TestCase
{
    public function testBasicEnumColumn()
    {
        $column = new EnumColumn('status', ['active', 'inactive']);
        $this->assertEquals(
            "status ENUM('active','inactive') NULL",
            $column->getSqlDefinition()
        );
    }

    public function testNonNullableEnumColumn()
    {
        $column = new EnumColumn('priority', ['low', 'medium', 'high'], false);
        $this->assertEquals(
            "priority ENUM('low','medium','high') NOT NULL",
            $column->getSqlDefinition()
        );
    }

    public function testEnumColumnWithDefault()
    {
        $column = new EnumColumn(
            'status',
            ['active', 'inactive'],
            true,
            'active'
        );
        $this->assertEquals(
            "status ENUM('active','inactive') NULL DEFAULT 'active'",
            $column->getSqlDefinition()
        );
    }

    public function testEnumColumnThrowsExceptionForInvalidDefault()
    {
        $this->expectException(UnsupportedColumnType::class);
        new EnumColumn('status', ['active', 'inactive'], true, 'pending');
    }
}