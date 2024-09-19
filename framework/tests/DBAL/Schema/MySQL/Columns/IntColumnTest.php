<?php

namespace Tests\DBAL\Schema\MySQL\Columns;

use DJWeb\Framework\DBAL\Schema\MySQL\Columns\IntColumn;
use PHPUnit\Framework\TestCase;

class IntColumnTest extends TestCase
{
    public function testBasicIntColumn()
    {
        $column = new IntColumn('age');
        $this->assertEquals(
            'age INT(11) NULL',
            $column->getSqlDefinition()
        );
    }

    public function testNonNullableIntColumn()
    {
        $column = new IntColumn('id', nullable: false);
        $this->assertEquals(
            'id INT(11) NOT NULL',
            $column->getSqlDefinition()
        );
    }

    public function testIntColumnWithDefault()
    {
        $column = new IntColumn('count', nullable: true, default: 0, length: 5);
        $this->assertEquals(
            'count INT(5) NULL DEFAULT 0',
            $column->getSqlDefinition()
        );
    }

    public function testUnsignedIntColumn()
    {
        $column = new IntColumn(
            'positive_num',
            nullable: false,
            length: 10,
            unsigned: true
        );
        $this->assertEquals(
            'positive_num INT(10) UNSIGNED NOT NULL',
            $column->getSqlDefinition()
        );
    }

    public function testAutoIncrementIntColumn()
    {
        $column = new IntColumn('id', nullable: false, autoIncrement: true);
        $this->assertEquals(
            'id INT(11) NOT NULL AUTO_INCREMENT',
            $column->getSqlDefinition()
        );
    }
}