<?php

namespace Tests\Console\Commands;

use DJWeb\Framework\Console\Application;
use DJWeb\Framework\Console\Output\Contacts\OutputContract;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use Tests\BaseTestCase;

class MakeFactoryTest extends BaseTestCase
{
    private Application $app;
    private OutputContract $output;
    public function testMakeFactory()
    {
        $factoryName = 'PostFactory';
        $file = 'PostFactory.php';
        $_SERVER['argv'] = ['console/bin', 'make:factory', $factoryName];

        $this->app->set(OutputContract::class, $this->output);

        $this->output->expects($this->once())
            ->method('info')
            ->with("Utworzono {$file}");

        $result = $this->app->handle();

        $this->assertEquals(0, $result);
        $this->assertFileExists(sys_get_temp_dir() . '/' . $file);

        // Sprawdzenie zawartości pliku
        $content = file_get_contents(sys_get_temp_dir() . '/' . $file);
        $this->assertStringContainsString('class PostFactory extends Factory', $content);
        $this->assertStringContainsString('return \Tests\Helpers\Models\Post::class;', $content);
    }
    public function setUp(): void
    {
        Application::withInstance(null);
        $this->app = Application::getInstance();
        $this->app->bind('app.root_namespace', 'Tests\\Helpers\\');
        $this->app->bind('app.factories_namespace', 'Models\\');
        $this->app->bind(
            'app.base_path',
            sys_get_temp_dir()
        );
        $this->app->bind('app.factories_path', sys_get_temp_dir());
        $this->output = $this->createMock(OutputContract::class);
        $this->app->set(ContainerContract::class, $this->app);

    }
}