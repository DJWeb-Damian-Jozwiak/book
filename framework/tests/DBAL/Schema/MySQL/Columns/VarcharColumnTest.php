<?php

namespace Tests\DBAL\Schema\MySQL\Columns;

use DJWeb\Framework\DBAL\Schema\MySQL\Columns\VarcharColumn;
use PHPUnit\Framework\TestCase;

class VarcharColumnTest extends TestCase
{
    public function testBasicVarcharColumn()
    {
        $column = new VarcharColumn('name');
        $this->assertEquals(
            'name VARCHAR(255) NULL',
            $column->getSqlDefinition()
        );
    }

    public function testNonNullableVarcharColumn()
    {
        $column = new VarcharColumn('email', nullable: false, length: 100);
        $this->assertEquals(
            'email VARCHAR(100) NOT NULL',
            $column->getSqlDefinition()
        );
    }

    public function testVarcharColumnWithDefault()
    {
        $column = new VarcharColumn('status', default: 'pending', length: 50);
        $this->assertEquals(
            "status VARCHAR(50) NULL DEFAULT 'pending'",
            $column->getSqlDefinition()
        );
    }
}