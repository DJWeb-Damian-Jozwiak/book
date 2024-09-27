<?php

namespace Tests\Console\Commands;

use DJWeb\Framework\Console\Application;
use DJWeb\Framework\Console\Commands\Migrate;
use DJWeb\Framework\Console\Output\Contacts\OutputContract;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\DBAL\Contracts\Migrations\MigrationExecutorContract;
use DJWeb\Framework\DBAL\Contracts\Migrations\MigrationRepositoryContract;
use DJWeb\Framework\DBAL\Contracts\Migrations\MigrationResolverContract;
use Tests\BaseTestCase;

class MigrateTest extends BaseTestCase
{
    private Application $app;
    private OutputContract $output;
    private MigrationRepositoryContract $repository;
    private MigrationResolverContract $resolver;
    private MigrationExecutorContract $executor;

    protected function setUp(): void
    {
        $this->app = Application::getInstance();
        $this->app->bind('base_path', sys_get_temp_dir());
        $this->app->set('PDO', $this->createMock(\PDO::class));
        $this->app->addBasePath(dirname(__DIR__, 3));
        if (! file_exists($this->app->getBasePath() . '/.env')) {
            file_put_contents($this->app->getBasePath() . '/.env', '');
        }
        $this->app->loadConfig();
        $this->app->bind('app.migrations_path', sys_get_temp_dir());

        $this->output = $this->createMock(OutputContract::class);
        $this->repository = $this->createMock(MigrationRepositoryContract::class);
        $this->resolver = $this->createMock(MigrationResolverContract::class);
        $this->executor = $this->createMock(MigrationExecutorContract::class);

        $this->app->set(OutputContract::class, $this->output);
        $this->app->set(ContainerContract::class, $this->app);
        /** @var Migrate $command */
        $command = $this->app->get('command.migrate');
        $command->withMigrationExecutor($this->executor);
        $command->withMigrationRepository($this->repository);
        $command->withMigrationResolver($this->resolver);
    }

    public function testMigrateWithNoPendingMigrations()
    {
        $this->repository->expects($this->once())
            ->method('createMigrationsTable');

        $this->resolver->expects($this->once())
            ->method('getMigrationFiles')
            ->willReturn(['migration1.php', 'migration2.php']);

        $this->repository->expects($this->once())
            ->method('getRan')
            ->willReturn(['migration1.php', 'migration2.php']);

        $this->output->expects($this->once())
            ->method('info')
            ->with('Nic do zmigrowania.');

        $_SERVER['argv'] = ['console/bin', 'migrate'];
        $result = $this->app->handle();

        $this->assertEquals(0, $result);
    }

    public function testMigrateWithPendingMigrations()
    {
        $this->repository->expects($this->once())
            ->method('createMigrationsTable');

        $this->resolver->expects($this->once())
            ->method('getMigrationFiles')
            ->willReturn(['migration1.php', 'migration2.php', 'migration3.php']);

        $this->repository->expects($this->once())
            ->method('getRan')
            ->willReturn(['migration1.php', 'migration2.php']);


        $_SERVER['argv'] = ['console/bin', 'migrate'];
        $result = $this->app->handle();

        $this->assertEquals(0, $result);
    }

    public function testMigrateWithStep()
    {
        $this->repository->expects($this->once())
            ->method('createMigrationsTable');

        $this->resolver->expects($this->once())
            ->method('getMigrationFiles')
            ->willReturn(['migration1.php', 'migration2.php', 'migration3.php']);

        $this->repository->expects($this->once())
            ->method('getRan')
            ->willReturn(['migration1.php']);

        $this->executor->expects($this->once())
            ->method('executeMigrations')
            ->with(['migration2.php'], 'up', false)
            ->willReturn(['migration2.php']);

        $this->repository->expects($this->once())
            ->method('log')
            ->with('migration2.php');

        $this->output->expects($this->once())
            ->method('info')
            ->with('Zmigrowano: migration2.php');

        $_SERVER['argv'] = ['console/bin', 'migrate', '--step=1'];
        $result = $this->app->handle();

        $this->assertEquals(0, $result);
    }

    public function tearDown(): void
    {
        $_SERVER = [];
        parent::tearDown();
    }
}