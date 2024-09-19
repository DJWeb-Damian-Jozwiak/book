<?php

namespace Tests\DBAL\Schema\MySQL\Columns;

use DJWeb\Framework\DBAL\Schema\MySQL\Columns\TextColumn;
use PHPUnit\Framework\TestCase;

class TextColumnTest extends TestCase
{
    public function testBasicTextColumn()
    {
        $column = new TextColumn('description');
        $this->assertEquals(
            'description TEXT NULL',
            $column->getSqlDefinition()
        );
    }

    public function testNonNullableTextColumn()
    {
        $column = new TextColumn('content', nullable: false);
        $this->assertEquals(
            'content TEXT NOT NULL',
            $column->getSqlDefinition()
        );
    }
}