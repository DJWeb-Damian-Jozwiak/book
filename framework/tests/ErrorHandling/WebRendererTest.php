<?php

namespace ErrorHandling;

use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\ErrorHandling\Renderers\WebRenderer;
use DJWeb\Framework\Web\Application;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tests\BaseTestCase;

class WebRendererTest extends BaseTestCase
{
    public function testRenderDebug()
    {
//        $app = Application::getInstance();
//        $app->bind('base_path', dirname(__DIR__));
//        $config = $this->createMock(ConfigContract::class);
//        $configMap = [
//            'views.default' => 'blade',
//            'views.engines.blade' => [
//                'paths' => [
//                    'template_path' => __DIR__ . '/../resources/views/blade',
//                    'cache_path' => __DIR__ . '/../storage/cache/blade',
//                ],
//                'components' => [
//                    'namespace' => '\\Tests\\Helpers\\View\\Components\\',
//                ]
//            ]
//        ];
//        $config->expects($this->exactly(2))
//            ->method('get')
//            ->willReturnCallback(fn(string $key) => $configMap[$key] ?? null);
//        $app->set(ConfigContract::class, $config);
//        $renderer = new WebRenderer(debug: true);
//        $response = $renderer->render(new RuntimeException('Test'));
    }
}