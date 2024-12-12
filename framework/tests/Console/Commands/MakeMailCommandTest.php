<?php

namespace Tests\Console\Commands;

use DJWeb\Framework\Console\Application;
use DJWeb\Framework\Console\Output\Contacts\OutputContract;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use PHPUnit\Framework\TestCase;

class MakeMailCommandTest extends TestCase
{
    private Application $app;
    private OutputContract $output;
    public function testMakeSeeder()
    {
        $seederName = 'TestMail';
        $file = 'TestMail.php';
        $_SERVER['argv'] = ['console/bin', 'make:mail', $seederName];

        $this->app->set(OutputContract::class, $this->output);

        $this->output->expects($this->once())
            ->method('info')
            ->with("Utworzono {$file}");

        $result = $this->app->handle();

        $this->assertEquals(0, $result);
        $this->assertFileExists(sys_get_temp_dir() . '/' . $file);

    }
    public function setUp(): void
    {
        Application::withInstance(null);
        $this->app = Application::getInstance();
        $this->app->bind(
            'app.base_path',
            sys_get_temp_dir()
        );
        $this->app->bind('base_path', sys_get_temp_dir());
        $this->app->bind('app.mail_path', sys_get_temp_dir());
        $this->output = $this->createMock(OutputContract::class);
        $this->app->set(ContainerContract::class, $this->app);

    }
}