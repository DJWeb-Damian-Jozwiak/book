<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Schema\MySQL\Columns;

use DJWeb\Framework\DBAL\Schema\Column;

class TextColumn extends Column
{
    public function __construct(
        string $name,
        bool $nullable = true,
    ) {
        parent::__construct($name, 'TEXT', $nullable);
    }

    public function getSqlDefinition(): string
    {
        $sql = "{$this->name} {$this->type}";
        $sql .= $this->nullable ? ' NULL' : ' NOT NULL';
        return $sql;
    }

    public function getSqlType(): string
    {
        return 'string';
    }
}
