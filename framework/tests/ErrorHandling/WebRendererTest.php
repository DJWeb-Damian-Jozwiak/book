<?php

declare(strict_types=1);

namespace Tests\ErrorHandling;

use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\ErrorHandling\Renderers\WebRenderer;
use DJWeb\Framework\Exceptions\Container\NotFoundError;
use DJWeb\Framework\Web\Application;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tests\BaseTestCase;

class WebRendererTest extends BaseTestCase
{
    public function testRenderProd()
    {
        $app = Application::getInstance();
        $app->bind('base_path', dirname(__DIR__));
        $config = $this->createMock(ConfigContract::class);
        $configMap = [
            'views.default' => 'blade',
            'views.engines.blade' => [
                'paths' => [
                    'template_path' => __DIR__ . '/../resources/views/blade',
                    'cache_path' => __DIR__ . '/../storage/cache/blade',
                ],
                'components' => [
                    'namespace' => '\\Tests\\Helpers\\View\\Components\\',
                ]
            ]
        ];
        $config->expects($this->any())
            ->method('get')
            ->willReturnCallback(fn(string $key) => $configMap[$key] ?? null);
        $app->set(ConfigContract::class, $config);
        $renderer = new WebRenderer(debug: false);
        $response = $renderer->render(new RuntimeException('Test'));
        $this->assertStringContainsString('500 Internal Server Error', $response->getBody()->getContents());
    }

    public function testRenderProd404()
    {
        $app = Application::getInstance();
        $app->bind('base_path', dirname(__DIR__));
        $config = $this->createMock(ConfigContract::class);
        $configMap = [
            'views.default' => 'blade',
            'views.engines.blade' => [
                'paths' => [
                    'template_path' => __DIR__ . '/../resources/views/blade',
                    'cache_path' => __DIR__ . '/../storage/cache/blade',
                ],
                'components' => [
                    'namespace' => '\\Tests\\Helpers\\View\\Components\\',
                ]
            ]
        ];
        $config->expects($this->any())
            ->method('get')
            ->willReturnCallback(fn(string $key) => $configMap[$key] ?? null);
        $app->set(ConfigContract::class, $config);
        $renderer = new WebRenderer(debug: false);
        $response = $renderer->render(new NotFoundError('test'));
        $this->assertStringContainsString('404 Not Found', $response->getBody()->getContents());
    }


    public function testRenderDebug()
    {
        $app = Application::getInstance();
        $app->bind('base_path', dirname(__DIR__));
        $config = $this->createMock(ConfigContract::class);
        $configMap = [
            'views.default' => 'blade',
            'views.engines.blade' => [
                'paths' => [
                    'template_path' => __DIR__ . '/../resources/views/blade',
                    'cache_path' => __DIR__ . '/../storage/cache/blade',
                ],
                'components' => [
                    'namespace' => '\\Tests\\Helpers\\View\\Components\\',
                ]
            ]
        ];
        $config->expects($this->any())
            ->method('get')
            ->willReturnCallback(fn(string $key) => $configMap[$key] ?? null);
        $app->set(ConfigContract::class, $config);
        $renderer = new WebRenderer(debug: true);
        $response = $renderer->render(new RuntimeException('Test'));
        $this->assertStringContainsString('RuntimeException', $response->getBody()->getContents());
    }
}