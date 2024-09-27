<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Migrations;

use DJWeb\Framework\Base\Application;
use DJWeb\Framework\DBAL\Contracts\Migrations\MigrationRepositoryContract;
use DJWeb\Framework\DBAL\Contracts\Query\QueryBuilderFacadeContract;
use DJWeb\Framework\DBAL\Query\Builders\QueryBuilder;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\IntColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\VarcharColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Schema;

class DatabaseMigrationRepository implements MigrationRepositoryContract
{
    private const MIGRATIONS_TABLE = 'migrations';
    private QueryBuilderFacadeContract $queryBuilder;

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
    }

    public function createMigrationsTable(): void
    {
        $schema = Application::getInstance()->get(Schema::class);

        if (! in_array(self::MIGRATIONS_TABLE, $schema->getTables())) {
            $schema->createTable(self::MIGRATIONS_TABLE, [
                new IntColumn('id', autoIncrement: true),
                new VarcharColumn('migration', true, length: 255),
                new IntColumn('batch'),
            ]);
        }
    }

    public function log(string $migration): void
    {
        $this->queryBuilder->insert(self::MIGRATIONS_TABLE)
            ->values([
                'migration' => $migration,
                'batch' => $this->getNextBatchNumber(),
            ]);
    }

    public function delete(string $migration): void
    {
        $this->queryBuilder->delete(self::MIGRATIONS_TABLE)
            ->where('migration', '=', $migration)
            ->delete();
    }

    public function getRan(): array
    {
        return array_column($this->getMigrations(), 'migration');
    }

    /**
     * @return array<int, string>
     */
    public function getMigrations(): array
    {
        $data = $this->queryBuilder->select(self::MIGRATIONS_TABLE)->get();
        return array_column($data, 'migration');
    }

    private function getNextBatchNumber(): int
    {
        $current = $this->queryBuilder->select(self::MIGRATIONS_TABLE)
            ->select([
                'max(batch) as batch',
            ])->first()['batch'] ?? 0;
        return $current + 1;
    }
}
