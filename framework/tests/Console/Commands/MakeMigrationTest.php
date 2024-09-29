<?php

namespace Tests\Console\Commands;

use Carbon\Carbon;
use DJWeb\Framework\Console\Application;
use DJWeb\Framework\Console\Output\Contacts\OutputContract;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use Tests\BaseTestCase;

class MakeMigrationTest extends BaseTestCase
{
    private Application $app;
    private OutputContract $output;

    public function testMakeMigration()
    {
        Carbon::setTestNow('2024-09-26 17:00:00');
        $file = '2024_09_26_170000_create_users_table.php';
        $migrationName = 'create_users_table';
        $_SERVER['argv'] = ['console/bin', 'make:migration', $migrationName];
        $this->app->set(OutputContract::class, $this->output);
        $this->output->expects($this->once())
            ->method('info')
            ->with("Utworzono {$file}");
        $result = $this->app->handle();
        $this->assertEquals(0, $result);
    }

    public function testMakeMigrationAddPrefix()
    {
        Carbon::setTestNow('2024-09-26 17:00:00');
        $file = '2024_09_26_170000_create_users_table.php';
        $migrationName = 'create_users';
        $_SERVER['argv'] = ['console/bin', 'make:migration', $migrationName];
        $this->app->set(OutputContract::class, $this->output);
        $this->output->expects($this->once())
            ->method('info')
            ->with("Utworzono {$file}");
        $result = $this->app->handle();
        $this->assertEquals(0, $result);
    }
    public function tearDown(): void
    {
        $_SERVER = [];
        parent::tearDown();
    }

    protected function setUp(): void
    {
        Application::withInstance(null);
        $this->app = Application::getInstance();
        $this->app->bind(
            'app.base_path',
            sys_get_temp_dir()
        );
        $this->app->bind('app.migrations_path', sys_get_temp_dir());
        $this->output = $this->createMock(OutputContract::class);
        $this->app->set(ContainerContract::class, $this->app);

    }
}