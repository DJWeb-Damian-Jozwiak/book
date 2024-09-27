<?php

declare(strict_types=1);

namespace DJWeb\Framework\ServiceProviders;

use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Container\ServiceProvider;
use DJWeb\Framework\DBAL\Contracts\Migrations\MigrationExecutorContract;
use DJWeb\Framework\DBAL\Contracts\Migrations\MigrationRepositoryContract;
use DJWeb\Framework\DBAL\Contracts\Migrations\MigrationResolverContract;
use DJWeb\Framework\DBAL\Contracts\Schema\SchemaContract;
use DJWeb\Framework\DBAL\Migrations\DatabaseMigrationRepository;
use DJWeb\Framework\DBAL\Migrations\MigrationExecutor;
use DJWeb\Framework\DBAL\Migrations\MigrationResolver;

class MigrationsServiceProvider extends ServiceProvider
{
    public function register(ContainerContract $container): void
    {
        /** @var string $path */
        $path = $container->getBinding('app.migrations_path') ?? '';
        /** @var SchemaContract $schema */
        $schema = $container->get(SchemaContract::class);
        $resolver = new MigrationResolver($path);
        $repository = new DatabaseMigrationRepository();
        $container->set(MigrationResolverContract::class, $resolver);
        $container->set(MigrationRepositoryContract::class, $repository);
        $container->set(
            MigrationExecutorContract::class,
            new MigrationExecutor($schema, $repository, $resolver)
        );
        $container->bind('app.root_namespace', 'App\\');
    }
}
