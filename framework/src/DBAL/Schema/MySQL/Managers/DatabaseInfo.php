<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Schema\MySQL\Managers;

use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Contracts\Schema\DatabaseInfoContract;
use DJWeb\Framework\Exceptions\DBAL\Schema\SchemaError;
use PDO;

readonly class DatabaseInfo implements DatabaseInfoContract
{
    public function __construct(private ConnectionContract $connection)
    {
    }

    public function getTables(): array
    {
        $sql = 'SHOW TABLES';
        $stmt = $this->connection->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function getColumns(string $tableName): array
    {
        if (empty($tableName)) {
            throw new SchemaError('Table name cannot be empty');
        }

        try {
            $stmt = $this->connection->query("DESCRIBE `{$tableName}`");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            throw new SchemaError(
                "Failed to get columns for table {$tableName}: " . $e->getMessage(
                )
            );
        }
    }
}
