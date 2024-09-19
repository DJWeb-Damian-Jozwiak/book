<?php

declare(strict_types=1);

namespace Tests\DBAL\Schema\MySQL\Columns;

use DJWeb\Framework\DBAL\Schema\MySQL\Columns\DateTimeColumn;
use PHPUnit\Framework\TestCase;

class DateTimeColumnTest extends TestCase
{
    public function testBasicDateTimeColumn()
    {
        $column = new DateTimeColumn('created_at');
        $this->assertEquals(
            'created_at DATETIME NULL',
            $column->getSqlDefinition()
        );
    }

    public function testNonNullableDateTimeColumn()
    {
        $column = new DateTimeColumn('updated_at', false);
        $this->assertEquals(
            'updated_at DATETIME NOT NULL',
            $column->getSqlDefinition()
        );
    }

    public function testDateTimeColumnWithDefault()
    {
        $column = new DateTimeColumn('logged_at', true, 'CURRENT_TIMESTAMP');
        $this->assertEquals(
            'logged_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP',
            $column->getSqlDefinition()
        );
    }

    public function testDateTimeColumnWithCurrentTimestamp()
    {
        $column = new DateTimeColumn('created_at', false);
        $column->current();
        $this->assertEquals(
            'created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            $column->getSqlDefinition()
        );
    }

    public function testDateTimeColumnWithOnUpdateCurrentTimestamp()
    {
        $column = new DateTimeColumn(
            'updated_at', false, currentOnUpdate: true
        );
        $this->assertEquals(
            'updated_at DATETIME NOT NULL ON UPDATE CURRENT_TIMESTAMP',
            $column->getSqlDefinition()
        );
    }
}