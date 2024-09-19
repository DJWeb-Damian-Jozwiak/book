<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Schema\MySQL\Columns;

use DJWeb\Framework\DBAL\Schema\Column;
use DJWeb\Framework\Exceptions\DBAL\Schema\UnsupportedColumnType;

class EnumColumn extends Column
{
    /**
     * @param array<int, ?string> $values
     */
    public function __construct(
        string $name,
        public readonly array $values,
        bool $nullable = true,
        mixed $default = null
    ) {
        if ($default !== null && ! in_array($default, $this->values)) {
            throw new UnsupportedColumnType();
        }
        parent::__construct($name, 'ENUM', $nullable, $default);
    }

    public function getSqlDefinition(): string
    {
        $values = implode(
            ',',
            array_map(static fn ($v) => "'{$v}'", $this->values)
        );
        $sql = "{$this->name} {$this->type}({$values})";
        $sql .= $this->nullable ? ' NULL' : ' NOT NULL';
        if ($this->default !== null) {
            $sql .= " DEFAULT '{$this->default}'";
        }
        return $sql;
    }
}
