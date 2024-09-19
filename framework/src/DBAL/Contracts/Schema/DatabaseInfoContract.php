<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Contracts\Schema;

interface DatabaseInfoContract
{
    public function getTables(): array;
    public function getColumns(string $tableName): array;
}
