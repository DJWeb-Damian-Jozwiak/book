<?php

namespace Tests;

use DJWeb\Framework\Config\Config;
use DJWeb\Framework\Config\ConfigBase;
use DJWeb\Framework\Web\Application;
use Dotenv\Exception\InvalidPathException;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function tearDown(): void
    {
        Application::withInstance(null);
    }

    public function testInvalidDirectory()
    {
        $app = Application::getInstance();
        $app->addBasePath('invalid_directory');
        $this->expectException(InvalidPathException::class);
        $app->loadConfig();
    }

    public function testLoadConfig()
    {
        $app = Application::getInstance();
        $app->bind('base_path', dirname(__DIR__));
        if (!file_exists($app->base_path . '/.env')) {
            file_put_contents($app->base_path . '/.env', '');
        }
        $this->assertInstanceOf(ConfigBase::class, $app->config);
    }

    public function testGetAndSet()
    {
        $app = Application::getInstance();
        $app->bind('base_path', __DIR__);
        if (!file_exists($app->base_path . '/.env')) {
            file_put_contents($app->base_path . '/.env', '');
        }
        Config::set('app.value.name', 'Aplikacja');
        $this->assertEquals('Aplikacja', Config::get('app.value.name'));
        $this->assertNull(Config::get('sample.entry2'));
    }

    public function testSetArray()
    {
        Config::set('app', ['name' => 'My App', 'version' => '1.0.0']);
        $this->assertEquals('My App', Config::get('app.name'));
        $this->assertEquals('1.0.0', Config::get('app.version'));
    }
}
