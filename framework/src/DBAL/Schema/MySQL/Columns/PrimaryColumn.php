<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Schema\MySQL\Columns;

use DJWeb\Framework\DBAL\Schema\Column;

class PrimaryColumn extends Column
{
    public function __construct(string $name = 'id')
    {
        parent::__construct($name, 'PRIMARY KEY');
    }
    public function getSqlDefinition(): string
    {
        return $this->type. ' (`' . $this->name . '`)';
    }

}
