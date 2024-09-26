<?php

namespace Tests\DBAL\Migrations;

use DJWeb\Framework\DBAL\Schema\MySQL\Schema;
use PHPUnit\Framework\TestCase;
use Tests\Helpers\TestMigration;

class MigrationTest extends TestCase
{
    public function testMigration()
    {
        $schema = $this->createMock(Schema::class);
        $migration = new TestMigration();
        $migration->withSchema($schema);
        $migration->withName('test');
        $this->assertEquals('test', $migration->getName());
    }
}