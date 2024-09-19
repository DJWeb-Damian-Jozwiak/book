<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Schema\MySQL\Columns;

use DJWeb\Framework\DBAL\Schema\Column;

class DateTimeColumn extends Column
{
    public function __construct(
        string $name,
        bool $nullable = true,
        mixed $default = null,
        private bool $current = false,
        private bool $currentOnUpdate = false,
    ) {
        parent::__construct($name, 'DATETIME', $nullable, $default);
        if ($this->current) {
            $this->current();
        }
        if ($this->currentOnUpdate) {
            $this->currentOnUpdate();
        }
    }

    public function current(): self
    {
        $this->default = 'CURRENT_TIMESTAMP';
        $this->current = true;
        return $this;
    }

    public function currentOnUpdate(): self
    {
        $this->currentOnUpdate = true;
        return $this;
    }

    public function getSqlDefinition(): string
    {
        $sql = "{$this->name} {$this->type}";
        $sql .= $this->nullable ? ' NULL' : ' NOT NULL';
        if ($this->default !== null) {
            $sql .= " DEFAULT {$this->default}";
        }
        if ($this->currentOnUpdate) {
            $sql .= ' ON UPDATE CURRENT_TIMESTAMP';
        }
        return $sql;
    }
}
