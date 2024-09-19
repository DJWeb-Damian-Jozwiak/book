<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Schema\MySQL\Managers\Builders;

use DJWeb\Framework\DBAL\Schema\Column;

class CreateTableBuilder
{
    /**
     * @param string $tableName
     * @param array<int, Column> $columns
     *
     * @return string
     */
    public function build(string $tableName, array $columns): string
    {
        $columnDefinitions = array_map(
            static fn (Column $column) => $column->getSqlDefinition(),
            $columns
        );
        return sprintf(
            'CREATE TABLE %s (%s);',
            $tableName,
            implode(', ', $columnDefinitions)
        );
    }
}
