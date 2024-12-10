<?php

namespace Tests\Routing;

use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\DBAL\Contracts\Query\SelectQueryBuilderContract;
use DJWeb\Framework\View\Engines\BladeAdapter;
use DJWeb\Framework\Web\Application;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\BaseTestCase;

class InertiaRenderTest extends BaseTestCase
{
    public function setUp(): void
    {
        Application::withInstance(null);
    }
    public function tearDown(): void
    {
        $_SERVER = [];
        parent::tearDown();
        BladeAdapter::$force_compile = false;
    }

    public static function envProvider()
    {
        return [
            'production' => ['production', 'InertiaRendering.vue'],
            'local' => ['local', 'InertiaRendering.vue'],
        ];
    }

    #[DataProvider('envProvider')]
    public function testRenderBlade(string $env, string $output): void
    {
        BladeAdapter::$force_compile = true;
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
        $_SERVER['REQUEST_URI'] = '/inertia/index';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $builder = $this->createMock(SelectQueryBuilderContract::class);
        $app = Application::getInstance();
        $app->set(SelectQueryBuilderContract::class, $builder);
        $config = $this->createMock(ConfigContract::class);
        $app->set(ConfigContract::class, $config);
        $config->expects($this->any())->method('get')->willReturnCallback(fn(string $key) => match ($key) {
            'views.engines.blade' => $returnedConfig,
            'views.default' => 'blade',
            'app.env' => $env,
            default => null
        });

        $app->bind('base_path', dirname(__DIR__));
        $app->loadRoutes('\\Tests\\Helpers', dirname(__DIR__) . '/Helpers');

        BladeAdapter::buildDefault()->clearCache(__DIR__ . '/../storage/cache/blade');
        $response = $app->handle();
        $this->assertStringContainsString($output, $response->getBody()->getContents());
    }
}