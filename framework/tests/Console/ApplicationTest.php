<?php

namespace Tests\Console;

use DJWeb\Framework\Console\Application;
use DJWeb\Framework\Console\Utils\CommandNamespace;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    public function tearDown(): void
    {
        Application::withInstance(null);
    }

    public function testCreate()
    {
        $path = dirname(__DIR__) . '/' . 'Helpers';
        $namespace = 'Tests\\Helpers';
        $app = Application::getInstance();
        $app->registerCommands(
            new CommandNamespace($namespace, $path)
        );
        $this->assertTrue($app->has('command.test'));

        $_SERVER['argv'] = [
            'input.php',
            'test',
            'test',
            '--required_option=1'
        ];
        $result = $app->handle();
        $this->assertEquals(0, $result);
    }
}