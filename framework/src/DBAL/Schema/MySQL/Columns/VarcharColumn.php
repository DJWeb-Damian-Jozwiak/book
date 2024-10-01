<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Schema\MySQL\Columns;

use DJWeb\Framework\DBAL\Schema\Column;

class VarcharColumn extends Column
{
    public function __construct(
        string $name,
        bool $nullable = true,
        mixed $default = null,
        public readonly int $length = 255,
    ) {
        parent::__construct($name, 'VARCHAR', $nullable, $default);
    }

    public function getSqlDefinition(): string
    {
        $sql = "{$this->name} {$this->type}({$this->length}) ";
        $sql .= $this->getNullableDefinition();
        return trim($sql . $this->getDefaultValueDefinition());
    }

    private function getNullableDefinition(): string
    {
        return $this->nullable ? 'NULL ' : 'NOT NULL ';
    }

    private function getDefaultValueDefinition(): string
    {
        return $this->default !== null ? "DEFAULT '{$this->default}' " : '';
    }

    public function getSqlColumn(): string
    {
        return 'string';
    }
}
