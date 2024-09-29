<?php

namespace Tests\DBAL\Schema\MySQL\Columns;

use DJWeb\Framework\DBAL\Schema\MySQL\Columns\PrimaryColumn;
use PHPUnit\Framework\TestCase;

class PrimaryColumnTest extends TestCase
{
    public function testPrimaryColumn()
    {
        $column = new PrimaryColumn();
        $this->assertEquals(
            "PRIMARY KEY (`id`)",
            $column->getSqlDefinition()
        );
    }
}