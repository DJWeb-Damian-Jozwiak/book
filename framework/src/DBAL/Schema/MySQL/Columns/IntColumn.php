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
        if ($this->unsigned) {
            $sql .= ' UNSIGNED';
        }
        $sql .= $this->nullable ? ' NULL' : ' NOT NULL';
        if ($this->default !== null) {
            $sql .= " DEFAULT {$this->default}";
        }
        if ($this->autoIncrement) {
            $sql .= ' AUTO_INCREMENT';
        }

        return $sql;
    }
}
