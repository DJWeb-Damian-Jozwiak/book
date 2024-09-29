<?php

namespace Tests;

use DJWeb\Framework\Config\Config;
use DJWeb\Framework\Config\ConfigBase;
use DJWeb\Framework\Web\Application;
use Dotenv\Exception\InvalidPathException;
use PHPUnit\Framework\TestCase;

class ConfigTest extends BaseTestCase
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
        $app->addBasePath(dirname(__DIR__));
        if (! file_exists($app->getBasePath() . '/.env')) {
            file_put_contents($app->getBasePath() . '/.env', '');
        }
        $app->loadConfig();
        $this->assertInstanceOf(ConfigBase::class, $app->getConfig());
    }

    public function testGetAndSet()
    {
        $app = Application::getInstance();
        $app->addBasePath(__DIR__);
        if (! file_exists($app->getBasePath() . '/.env')) {
            file_put_contents($app->getBasePath() . '/.env', '');
        }
        $app->loadConfig();
        Config::set('app.value.name', 'Aplikacja');
        $this->assertEquals('Aplikacja', Config::get('app.value.name'));
        $this->assertNull(Config::get('sample.entry2'));
    }
}
