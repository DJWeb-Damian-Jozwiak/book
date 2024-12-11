<?php

namespace Tests\Console\Commands;

use DJWeb\Framework\Console\Application;
use DJWeb\Framework\Console\Output\Contacts\OutputContract;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use Tests\BaseTestCase;

class KeyGenerateCommandTest extends BaseTestCase
{
    private Application $app;

    public function testKeyGenerate()
    {
        $_SERVER['argv'] = ['console/bin', 'key:generate'];
        $this->app->handle();
        $content = file_get_contents($this->app->base_path . '/.env');
        $this->assertStringContainsString('APP_KEY=', $content);
    }

    public function setUp(): void
    {
        Application::withInstance(null);
        $this->app = Application::getInstance();
        $this->app->bind('app.root_namespace', 'Tests\\Helpers\\');
        $this->app->bind('app.seeder_namespace', 'Seeders\\');
        $this->app->bind('base_path', dirname(__DIR__, 2));
        $this->app->bind('app.factories_path', sys_get_temp_dir());
        $this->app->set(ContainerContract::class, $this->app);

    }
}