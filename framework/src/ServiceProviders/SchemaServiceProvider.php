<?php

declare(strict_types=1);

namespace DJWeb\Framework\ServiceProviders;

use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Container\ServiceProvider;
use DJWeb\Framework\DBAL\Connection\MySqlConnection;
use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Contracts\Schema\ColumnFactoryContract;
use DJWeb\Framework\DBAL\Contracts\Schema\ColumnManagerContract;
use DJWeb\Framework\DBAL\Contracts\Schema\DatabaseInfoContract;
use DJWeb\Framework\DBAL\Contracts\Schema\IndexManagerContract;
use DJWeb\Framework\DBAL\Contracts\Schema\SchemaContract;
use DJWeb\Framework\DBAL\Contracts\Schema\TableManagerContract;
use DJWeb\Framework\DBAL\Schema\MySQL\Schema;
use DJWeb\Framework\DBAL\Schema\SchemaFactory;

class SchemaServiceProvider extends ServiceProvider
{
    public function register(ContainerContract $container): void
    {
        $connection = new MySqlConnection();
        $container->set(ConnectionContract::class, $connection);
        /** @var Schema $schema */
        $schema = SchemaFactory::create($connection);
        $container->set(SchemaContract::class, $schema);
        $container->set(TableManagerContract::class, $schema->tableManager);
        $container->set(ColumnManagerContract::class, $schema->columnManager);
        $container->set(IndexManagerContract::class, $schema->indexManager);
        $container->set(DatabaseInfoContract::class, $schema->databaseInfo);
        $container->set(ColumnFactoryContract::class, $schema->columnFactory);
    }
}
