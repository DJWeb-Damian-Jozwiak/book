<?php

namespace Routing;

use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\DBAL\Contracts\Query\SelectQueryBuilderContract;
use DJWeb\Framework\Web\Application;
use Tests\BaseTestCase;

class TwigControllerTest extends BaseTestCase
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
    public function testDispatchToControllerSimpleRoute(): void
    {
        $returnedConfig = [
            'template_path' => __DIR__ . '/../resources/views/twig',
            'cache_path' => __DIR__ . '/../storage/cache/twig',
        ];
        $_SERVER = ['SERVER_NAME' => 'example.com', 'SERVER_PORT' => '443'];
        $_SERVER['REQUEST_URI'] = '/twig/index';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $builder = $this->createMock(SelectQueryBuilderContract::class);
        $app = Application::getInstance();
        $app->set(SelectQueryBuilderContract::class, $builder);
        $config = $this->createMock(ConfigContract::class);
        $app->set(ConfigContract::class, $config);
        $config->expects($this->any())->method('get')->willReturn($returnedConfig);

        $app->bind('base_path', dirname(__DIR__));
        $app->loadRoutes('\\Tests\\Helpers', dirname(__DIR__) . '/Helpers');

        $response = $app->handle();
        $this->assertEquals('<h1>hello test twig</h1>', $response->getBody()->getContents());
    }

    public function testUnknown(): void
    {
        $returnedConfig = [
            'template_path' => __DIR__ . '/../resources/views/twig',
            'cache_path' => __DIR__ . '/../storage/cache/twig',
        ];
        $_SERVER = ['SERVER_NAME' => 'example.com', 'SERVER_PORT' => '443'];
        $_SERVER['REQUEST_URI'] = '/twig/unknown-adapter';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $builder = $this->createMock(SelectQueryBuilderContract::class);
        $app = Application::getInstance();
        $app->set(SelectQueryBuilderContract::class, $builder);
        $config = $this->createMock(ConfigContract::class);
        $app->set(ConfigContract::class, $config);
        $config->expects($this->any())->method('get')->willReturn($returnedConfig);

        $app->bind('base_path', dirname(__DIR__));
        $app->loadRoutes('\\Tests\\Helpers', dirname(__DIR__) . '/Helpers');

        $this->expectException(\Exception::class);
        $app->handle();
    }
}