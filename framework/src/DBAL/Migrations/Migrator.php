<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Migrations;

use DJWeb\Framework\DBAL\Contracts\Migrations\MigrationRepositoryContract;
use DJWeb\Framework\DBAL\Contracts\Migrations\MigrationResolverContract;

class Migrator
{
    public function __construct(
        private MigrationRepositoryContract $repository,
        private MigrationResolverContract $resolver,
        private MigrationExecutor $executor
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function run(bool $pretend = false): array
    {
        $this->repository->createMigrationsTable();

        $files = $this->resolver->getMigrationFiles();
        $ran = $this->repository->getRan();
        $migrations = array_diff($files, $ran);

        return $this->executor->executeMigrations($migrations, 'up', $pretend);
    }

    /**
     * @return array<int, string>
     */
    public function rollback(bool $pretend = false): array
    {
        $ran = $this->repository->getRan();
        $files = $this->resolver->getMigrationFiles();
        $migrations = array_intersect($files, $ran);

        return $this->executor->executeMigrations(
            array_reverse($migrations),
            'down',
            $pretend
        );
    }
}
