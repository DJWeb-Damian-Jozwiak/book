<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Migrations;

use DJWeb\Framework\DBAL\Contracts\Migrations\MigrationRepositoryContract;
use DJWeb\Framework\DBAL\Contracts\Schema\SchemaContract;

class MigrationExecutor
{
    public function __construct(
        private SchemaContract $schema,
        private MigrationRepositoryContract $repository,
        private MigrationResolver $resolver
    ) {
    }

    /**
     * @param array<int, string> $migrations
     *
     * @return array<int, string>
     */
    public function executeMigrations(
        array $migrations,
        string $direction,
        bool $pretend
    ): array {
        $method = $direction === 'up' ? 'runUp' : 'runDown';
        $executed = [];

        foreach ($migrations as $migration) {
            $this->$method($migration, $pretend);
            $executed[] = $migration;
        }

        return $executed;
    }

    private function runUp(string $file, bool $pretend): void
    {
        $migration = $this->resolver->resolve($file);

        if (! $pretend) {
            $migration->withSchema($this->schema);
            $migration->up();
            $this->repository->log($migration->getName());
        }
    }

    private function runDown(string $file, bool $pretend): void
    {
        $migration = $this->resolver->resolve($file);

        if (! $pretend) {
            $migration->withSchema($this->schema);
            $migration->down();
            $this->repository->delete($file);
        }
    }
}
