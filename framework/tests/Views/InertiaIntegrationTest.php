<?php

declare(strict_types=1);

namespace Tests\Views;

use DJWeb\Framework\Config\Config;
use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\DBAL\Contracts\Query\SelectQueryBuilderContract;
use DJWeb\Framework\Routing\Router;
use DJWeb\Framework\View\Engines\BladeAdapter;
use DJWeb\Framework\View\Inertia\Inertia;
use DJWeb\Framework\Web\Application;
use Tests\BaseTestCase;

class InertiaIntegrationTest extends BaseTestCase
{
    private Application $app;
    private ConfigContract $config;

    protected function setUp(): void
    {
        parent::setUp();
        BladeAdapter::$force_compile = true;
    }

    public function testBasicInertiaRequest(): void
    {
        $_SERVER = [
            'SERVER_NAME' => 'example.com',
            'SERVER_PORT' => '443',
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/inertia/index'
        ];

        $this->setUpApplication();

        $response = $this->app->handle();
        $content = $response->getBody()->getContents();

        $this->assertStringContainsString('<title>TestApp</title>', $content);
        $this->assertStringContainsString('<div id="app"', $content);
        $this->assertStringContainsString('window.page', $content);
    }

    public function testNestedComponentRendering(): void
    {
        $_SERVER = [
            'SERVER_NAME' => 'example.com',
            'SERVER_PORT' => '443',
            'REQUEST_METHOD' => 'GET',
        ];
        $_SERVER['REQUEST_URI'] = '/inertia/nested';

        $this->setUpApplication();
        $response = $this->app->handle();
        $content = $response->getBody()->getContents();

        $this->assertStringContainsString('nestedProp', $content);
    }

    public function testInertiaXhrRequest(): void
    {
        $_SERVER = [
            'SERVER_NAME' => 'example.com',
            'SERVER_PORT' => '443',
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/inertia/index',
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
        ];

        $this->setUpApplication();

        $response = $this->app->handle();
        $content = $response->getBody()->getContents();
        $data = json_decode($content, true);

        $this->assertEquals('true', $response->getHeader('X-Inertia')[0]);
        $this->assertEquals('Accept', $response->getHeader('Vary')[0]);
        $this->assertArrayHasKey('component', $data);
        $this->assertArrayHasKey('props', $data);
        $this->assertArrayHasKey('url', $data);
        $this->assertArrayHasKey('version', $data);
    }

    public function testInertiaRequestWithSharedData(): void
    {
        $_SERVER = [
            'SERVER_NAME' => 'example.com',
            'SERVER_PORT' => '443',
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/inertia/shared',
        ];

        $this->setUpApplication();

        $response = $this->app->handle();
        $content = $response->getBody()->getContents();

        $this->assertStringContainsString('sharedProp', $content);
        $this->assertStringContainsString('sharedValue', $content);
    }

    public function testInertiaLocationResponse(): void
    {
        $_SERVER = [
            'SERVER_NAME' => 'example.com',
            'SERVER_PORT' => '443',
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/inertia/redirect',
            'HTTP_X_INERTIA' => 'true',
            'HTTP_ACCEPT' => 'text/html',
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
        ];



        $this->setUpApplication();

        $response = $this->app->handle();

        $this->assertEquals(409, $response->getStatusCode());
        $this->assertStringContainsString('/inertia/redirect', $response->getHeader('X-Inertia-Location')[0]);
    }



    private function setUpApplication(): void
    {
        // Setup blade configuration
        $bladeConfig = [
            'paths' => [
                'template_path' => __DIR__ . '/../resources/views/blade',
                'cache_path' => __DIR__ . '/../storage/cache/blade',
            ],
            'components' => [
                'namespace' => '\\Tests\\Helpers\\View\\Components\\',
            ]
        ];

        $this->app = Application::getInstance();

        // Setup mock builder
        $builder = $this->createMock(SelectQueryBuilderContract::class);
        $this->app->set(SelectQueryBuilderContract::class, $builder);

        // Setup config
        $this->config = $this->createMock(ConfigContract::class);
        $this->app->set(ConfigContract::class, $this->config);

        $this->config->expects($this->any())
            ->method('get')
            ->willReturnCallback(fn(string $key) => match ($key) {
                'views.engines.blade' => $bladeConfig,
                'views.default' => 'blade',
                'app.env' => 'testing',
                'app.name' => 'TestApp',
                default => null
            });

        $this->app->bind('base_path', dirname(__DIR__));
        $this->app->loadRoutes('\\Tests\\Helpers', dirname(__DIR__) . '/Helpers');

        BladeAdapter::buildDefault()->clearCache(__DIR__ . '/../storage/cache/blade');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $_SERVER = [];
    }
}