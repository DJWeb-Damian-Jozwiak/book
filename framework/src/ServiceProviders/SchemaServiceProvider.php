<?php

declare(strict_types=1);

namespace DJWeb\Framework\ServiceProviders;

use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Container\ServiceProvider;
use DJWeb\Framework\DBAL\Connection\MySqlConnection;
use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Contracts\Query\DeleteQueryBuilderContract;
use DJWeb\Framework\DBAL\Contracts\Query\InsertQueryBuilderContract;
use DJWeb\Framework\DBAL\Contracts\Query\SelectQueryBuilderContract;
use DJWeb\Framework\DBAL\Contracts\Query\UpdateQueryBuilderContract;
use DJWeb\Framework\DBAL\Contracts\Schema\SchemaContract;
use DJWeb\Framework\DBAL\Query\Builders\DeleteQueryBuilder;
use DJWeb\Framework\DBAL\Query\Builders\InsertQueryBuilder;
use DJWeb\Framework\DBAL\Query\Builders\SelectQueryBuilder;
use DJWeb\Framework\DBAL\Query\Builders\UpdateQueryBuilder;
use DJWeb\Framework\DBAL\Schema\SchemaFactory;

class SchemaServiceProvider extends ServiceProvider
{
    public function register(ContainerContract $container): void
    {
        $connection = new MySqlConnection();
        $container->set(ConnectionContract::class, $connection);
        $container->set(
            SchemaContract::class,
            SchemaFactory::create($connection)
        );
        $container->set(SelectQueryBuilderContract::class, new SelectQueryBuilder($connection));
        $container->set(InsertQueryBuilderContract::class, new InsertQueryBuilder($connection));
        $container->set(DeleteQueryBuilderContract::class, new DeleteQueryBuilder($connection));
        $container->set(UpdateQueryBuilderContract::class, new UpdateQueryBuilder($connection));
    }
}
