<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Schema\MySQL\Managers;

use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Contracts\Schema\DatabaseInfoContract;
use DJWeb\Framework\DBAL\Schema\Column;
use DJWeb\Framework\Exceptions\DBAL\Schema\SchemaError;
use PDO;

readonly class DatabaseInfo implements DatabaseInfoContract
{
    public function __construct(private ConnectionContract $connection)
    {
    }

    /**
     * @return array<int, string>
     */
    public function getTables(): array
    {
        $sql = 'SHOW TABLES';
        $stmt = $this->connection->query($sql);
        /** @phpstan-ignore-next-line */
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * @return array<int, Column>
     */
    public function getColumns(string $tableName): array
    {
        try {
            $factory = new ColumnFactory();
            $stmt = $this->connection->query("DESCRIBE `{$tableName}`");
            /** @phpstan-ignore-next-line */
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map(
                static fn (array $row) => $factory->createFromDescription($row),
                $data
            );
        } catch (\Throwable $e) {
            throw new SchemaError(
                "Failed to get columns for table {$tableName}: " . $e->getMessage(
                )
            );
        }
    }
}
