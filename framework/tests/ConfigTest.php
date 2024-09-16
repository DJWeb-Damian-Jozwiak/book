<?php

namespace Tests;

use DJWeb\Framework\Application;
use DJWeb\Framework\Config\Config;
use DJWeb\Framework\Config\ConfigBase;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testLoadConfig()
    {
        $app = Application::getInstance();
        $app->addBasePath(dirname(__DIR__));
        if (!file_exists($app->getBasePath() . '/.env')) {
            file_put_contents($app->getBasePath() . '/.env', '');
        }
        $app->loadConfig();
        $this->assertInstanceOf(ConfigBase::class, $app->getConfig());
    }

    public function testGetAndSet()
    {
        $app = Application::getInstance();
        $app->addBasePath(__DIR__);
        if (!file_exists($app->getBasePath() . '/.env')) {
            file_put_contents($app->getBasePath() . '/.env', '');
        }
        $app->loadConfig();
        Config::set('app.value.name', 'Aplikacja');
        $this->assertEquals('Aplikacja', Config::get('app.value.name'));
        $this->assertNull(Config::get('sample.entry2'));
    }
}
