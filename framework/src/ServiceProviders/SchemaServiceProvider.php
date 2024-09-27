<?php

declare(strict_types=1);

namespace DJWeb\Framework\ServiceProviders;

use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Container\ServiceProvider;
use DJWeb\Framework\DBAL\Connection\MySqlConnection;
use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Contracts\Schema\SchemaContract;
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
    }
}
