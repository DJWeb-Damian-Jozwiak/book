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
    }

    public function current(): self
    {
        $this->default = 'CURRENT_TIMESTAMP';
        $this->current = true;
        return $this;
    }

    public function getSqlDefinition(): string
    {
        $sql = "{$this->name} {$this->type} ";
        $sql .= $this->getNullableDefinition();
        $sql .= $this->getDefaultValueDefinition();
        return trim($sql . $this->getOnUpdateDefinition());
    }

    private function getNullableDefinition(): string
    {
        return $this->nullable ? 'NULL ' : 'NOT NULL ';
    }

    private function getDefaultValueDefinition(): string
    {
        return $this->default !== null ? "DEFAULT {$this->default} " : '';
    }

    private function getOnUpdateDefinition(): string
    {
        return $this->currentOnUpdate ? 'ON UPDATE CURRENT_TIMESTAMP' : '';
    }
}
