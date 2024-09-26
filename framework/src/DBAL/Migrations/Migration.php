<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Migrations;

use DJWeb\Framework\DBAL\Contracts\Migrations\MigrationContract;
use DJWeb\Framework\DBAL\Contracts\Schema\SchemaContract;

abstract class Migration implements MigrationContract
{
    protected SchemaContract $schema;
    protected string $name;

    public function __construct()
    {
    }

    public function withName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function withSchema(SchemaContract $schema): void
    {
        $this->schema = $schema;
    }
}
