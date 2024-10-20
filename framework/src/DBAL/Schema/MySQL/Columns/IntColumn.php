<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Schema\MySQL\Columns;

use DJWeb\Framework\DBAL\Schema\Column;

class IntColumn extends Column
{
    public function __construct(
        string $name,
        bool $nullable = true,
        mixed $default = null,
        public int $length = 11,
        public readonly bool $unsigned = false,
        public readonly bool $autoIncrement = false,
    ) {
        parent::__construct($name, 'INT', $nullable, $default);
    }

    public function getSqlDefinition(): string
    {
        $sql = "{$this->name} {$this->type}({$this->length})";
        $sql .= $this->getUnsignedSqlDefinition();
        $sql .= $this->getNullableSqlDefinition();
        $sql .= $this->getDefaultValueSqlDefinition();
        return $sql . $this->getAutoIncrementSqlDefinition();
    }

    public function getSqlColumn(): string
    {
        return 'int';
    }

    private function getUnsignedSqlDefinition(): string
    {
        return $this->unsigned ? ' UNSIGNED' : '';
    }

    private function getNullableSqlDefinition(): string
    {
        return $this->nullable ? ' NULL' : ' NOT NULL';
    }

    private function getDefaultValueSqlDefinition(): string
    {
        return $this->default !== null ? " DEFAULT {$this->default}" : '';
    }

    private function getAutoIncrementSqlDefinition(): string
    {
        return $this->autoIncrement ? ' AUTO_INCREMENT' : '';
    }
}
