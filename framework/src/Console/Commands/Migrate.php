<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console\Commands;

use DJWeb\Framework\Console\Attributes\AsCommand;
use DJWeb\Framework\Console\Attributes\CommandOption;
use DJWeb\Framework\Console\Command;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\DBAL\Contracts\Migrations\MigrationExecutorContract;
use DJWeb\Framework\DBAL\Contracts\Migrations\MigrationRepositoryContract;
use DJWeb\Framework\DBAL\Contracts\Migrations\MigrationResolverContract;

#[AsCommand(name: 'migrate')]
class Migrate extends Command
{
    #[CommandOption(name: 'database', description: 'Baza danych do migracji')]
    protected ?string $database = null;

    #[CommandOption(name: 'force', description: 'Wymuś migrację w środowisku produkcyjnym')]
    protected bool $force = false;

    #[CommandOption(name: 'step', description: 'Liczba migracji do wykonania')]
    protected ?int $step = null;
    private MigrationRepositoryContract $repository;
    private MigrationResolverContract $resolver;
    private MigrationExecutorContract $executor;

    public function __construct(
        ContainerContract $container,
    ) {
        parent::__construct($container);
        $repository = $this->container->get(MigrationRepositoryContract::class);
        $resolver = $this->container->get(MigrationResolverContract::class);
        $executor = $this->container->get(MigrationExecutorContract::class);
        $this->repository = $repository;
        $this->resolver = $resolver;
        $this->executor = $executor;
    }

    public function withMigrationRepository(MigrationRepositoryContract $repository): void
    {
        $this->repository = $repository;
    }

    public function withMigrationResolver(MigrationResolverContract $resolver): void
    {
        $this->resolver = $resolver;
    }

    public function withMigrationExecutor(MigrationExecutorContract $executor): void
    {
        $this->executor = $executor;
    }

    public function run(): int
    {
        $this->repository->createMigrationsTable();

        $migrations = $this->getPendingMigrations();

        if (! $migrations) {
            $this->getOutput()->info('Nic do zmigrowania.');
            return 0;
        }

        $batches = array_slice($migrations, 0, $this->step);

        $this->runMigrationBatch($batches);

        return 0;
    }

    /**
     * @return array<int, string>
     */
    private function getPendingMigrations(): array
    {
        $files = $this->resolver->getMigrationFiles();
        $ran = $this->repository->getRan();

        return array_diff($files, $ran);
    }

    /**
     * @param array<int, string> $batch
     *
     * @return void
     */
    private function runMigrationBatch(array $batch): void
    {
        $migrations = $this->executor->executeMigrations(
            $batch,
            'up',
        );

        foreach ($migrations as $migration) {
            $this->repository->log($migration);

            $this->getOutput()->info("Zmigrowano: {$migration}");
        }
    }
}
