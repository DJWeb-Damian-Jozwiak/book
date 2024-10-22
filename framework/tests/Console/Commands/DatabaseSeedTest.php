<?php

namespace Tests\Console\Commands;

use DJWeb\Framework\Console\Application;
use DJWeb\Framework\Console\Output\Contacts\OutputContract;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\DBAL\Contracts\Schema\DatabaseInfoContract;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\DateTimeColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\PrimaryColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\VarcharColumn;
use Tests\BaseTestCase;

class DatabaseSeedTest extends BaseTestCase
{
    private Application $app;
    private OutputContract $output;

    public function testSeed()
    {
        $_SERVER['argv'] = ['console/bin', 'database:seed', 'TestSeeder'];

        $this->app->set(OutputContract::class, $this->output);

        $this->output->expects($this->once())
            ->method('info')
            ->with("Database seeding completed successfully.");

        $this->app->handle();
    }

    public function testSeedWithInvalidSeeder()
    {
        $_SERVER['argv'] = ['console/bin', 'database:seed', 'MissingSeeder'];

        $this->app->set(OutputContract::class, $this->output);

        $this->output->expects($this->once())
            ->method('error')
            ->with("Seeder class Tests\Helpers\Seeders\MissingSeeder does not exist.");

        $this->app->handle();
    }
    public function setUp(): void
    {
        Application::withInstance(null);
        $this->app = Application::getInstance();
        $this->app->bind('app.root_namespace', 'Tests\\Helpers\\');
        $this->app->bind('app.seeder_namespace', 'Seeders\\');
        $this->app->bind(
            'app.base_path',
            sys_get_temp_dir()
        );
        $this->app->bind('app.factories_path', sys_get_temp_dir());
        $this->output = $this->createMock(OutputContract::class);
        $this->app->set(ContainerContract::class, $this->app);

    }
}