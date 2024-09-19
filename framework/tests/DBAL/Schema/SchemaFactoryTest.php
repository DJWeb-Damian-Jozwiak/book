<?php

namespace Tests\DBAL\Schema;

use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Schema\MySQL\Schema;
use DJWeb\Framework\DBAL\Schema\SchemaFactory;
use PHPUnit\Framework\TestCase;

class SchemaFactoryTest extends TestCase
{
    public function testCreateSchema()
    {
        $connection = $this->createMock(ConnectionContract::class);
        $factory = SchemaFactory::create($connection);
        $this->assertInstanceOf(Schema::class, $factory);
    }
}