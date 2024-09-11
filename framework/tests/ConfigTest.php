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
        $app->base_path = dirname(__DIR__);
        if (! file_exists($app->base_path . '/.env')) {
            file_put_contents($app->base_path . '/.env', '');
        }
        $app->loadConfig();
        $this->assertInstanceOf(ConfigBase::class, $app->getConfig());
    }

    public function testGetAndSet()
    {
        $app = Application::getInstance();
        $app->base_path = __DIR__;
        if (! file_exists($app->base_path . '/.env')) {
            file_put_contents($app->base_path . '/.env', '');
        }
        $app->loadConfig();
        Config::set('app.value.name', 'Aplikacja');
        $this->assertEquals('Aplikacja', Config::get('app.value.name'));
        $this->assertEquals('test value', Config::get('sample.entry'));
        $this->assertNull(Config::get('sample.entry2'));
    }
}