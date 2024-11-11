<?php

namespace Tests\Routing;

use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\Web\Application;
use Tests\BaseTestCase;
class ControllerTest extends BaseTestCase
{
    public function tearDown(): void
    {
        $_SERVER = [];
        parent::tearDown();
    }
    public function testDispatchToControllerSimpleRoute(): void
    {
        $_SERVER = ['SERVER_NAME' => 'example.com', 'SERVER_PORT' => '443'];
        $_SERVER['REQUEST_URI'] = '/test-with-attributes/test1';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $app = Application::getInstance();
        $config = $this->createMock(ConfigContract::class);
        $config->expects($this->any())
            ->method('get')
            ->with('middleware')
            ->willReturn([
                'before_global' => [],
                'global' => [],
                'after_global' => [],
            ]);
        $app->bind('base_path', dirname(__DIR__));
        $app->loadRoutes('\\Tests\\Helpers', dirname(__DIR__) . '/Helpers');

        $response = $app->handle();
        $this->assertEquals('test1', $response->getBody()->getContents());
    }

    public function testDispatchToControllerComplexRoute(): void
    {
        $_SERVER = ['SERVER_NAME' => 'example.com', 'SERVER_PORT' => '443'];
        $_SERVER['REQUEST_URI'] = '/test-with-attributes/test2/abcde';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $app = Application::getInstance();
        $config = $this->createMock(ConfigContract::class);
        $config->expects($this->any())
            ->method('get')
            ->with('middleware')
            ->willReturn([
                'before_global' => [],
                'global' => [],
                'after_global' => [],
            ]);
        $app->bind('base_path', dirname(__DIR__));
        $app->loadRoutes('\\Tests\\Helpers', dirname(__DIR__) . '/Helpers');

        $response = $app->handle();
        $this->assertEquals('abcde', $response->getBody()->getContents());
    }
}