<?php

namespace Routing;

use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\DBAL\Contracts\Query\SelectQueryBuilderContract;
use DJWeb\Framework\View\Engines\BladeAdapter;
use DJWeb\Framework\Web\Application;
use Tests\BaseTestCase;

class BladeControllerTest extends BaseTestCase
{
    public function setUp(): void
    {
        Application::withInstance(null);
    }
    public function tearDown(): void
    {
        $_SERVER = [];
        parent::tearDown();
    }

    public function testRenderBlade(): void
    {
        $returnedConfig = [
            'paths' => [
                'template_path' => __DIR__ . '/../resources/views/blade',
                'cache_path' => __DIR__ . '/../storage/cache/blade',
            ],
            'components' => [
                'namespace' => '\\Tests\\Helpers\\View\\Components\\',
            ]
        ];
        $_SERVER = ['SERVER_NAME' => 'example.com', 'SERVER_PORT' => '443'];
        $_SERVER['REQUEST_URI'] = '/blade/index';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $builder = $this->createMock(SelectQueryBuilderContract::class);
        $app = Application::getInstance();
        $app->set(SelectQueryBuilderContract::class, $builder);
        $config = $this->createMock(ConfigContract::class);
        $app->set(ConfigContract::class, $config);
        $config->expects($this->any())->method('get')->willReturn($returnedConfig);

        $app->bind('base_path', dirname(__DIR__));
        $app->loadRoutes('\\Tests\\Helpers', dirname(__DIR__) . '/Helpers');

        BladeAdapter::buildDefault()->clearCache(__DIR__ . '/../storage/cache/blade');
        $response = $app->handle();
        $this->assertStringContainsString('Hello world blade', $response->getBody()->getContents());
        $this->assertStringContainsString('</button>', $response->getBody()->getContents());
        BladeAdapter::buildDefault()->clearCache(__DIR__ . '/../storage/cache/blade');
    }

    public function testMissingBladeTemplate(): void
    {
        $returnedConfig = [
            'paths' => [
                'template_path' => __DIR__ . '/../resources/views/blade',
                'cache_path' => __DIR__ . '/../storage/cache/blade',
            ],
            'components' => [
                'namespace' => '\\Tests\\Helpers\\View\\Components\\',
            ]
        ];
        $_SERVER = ['SERVER_NAME' => 'example.com', 'SERVER_PORT' => '443'];
        $_SERVER['REQUEST_URI'] = '/blade/missing';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $builder = $this->createMock(SelectQueryBuilderContract::class);
        $app = Application::getInstance();
        $app->set(SelectQueryBuilderContract::class, $builder);
        $config = $this->createMock(ConfigContract::class);
        $app->set(ConfigContract::class, $config);
        $config->expects($this->any())->method('get')->willReturn($returnedConfig);

        $app->bind('base_path', dirname(__DIR__));
        $app->loadRoutes('\\Tests\\Helpers', dirname(__DIR__) . '/Helpers');
        $this->expectException(\RuntimeException::class);
        $app->handle();
    }
}