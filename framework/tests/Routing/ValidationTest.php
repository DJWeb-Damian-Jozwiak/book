<?php

namespace Routing;

use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\Exceptions\Validation\ValidationError;
use DJWeb\Framework\Web\Application;
use Tests\BaseTestCase;

class ValidationTest extends BaseTestCase
{
    public function tearDown(): void
    {
        $_SERVER = [];
        $_POST = [];
        parent::tearDown();
    }
    public function testValidationFails(): void
    {
        $_SERVER = ['SERVER_NAME' => 'example.com', 'SERVER_PORT' => '443'];
        $_SERVER['REQUEST_URI'] = '/validation/index';
        $_SERVER['REQUEST_METHOD'] = 'POST';
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
        $this->expectException(ValidationError::class);
        $app->handle();
    }

    public function testValidationPasses(): void
    {
        $_SERVER = ['SERVER_NAME' => 'example.com', 'SERVER_PORT' => '443'];
        $_SERVER['REQUEST_URI'] = '/validation/index';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['name' => 'test', 'email' => 'test@example.com', 'age' => 25];
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
        $request = $app->handle();
        $this->assertJson(
            json_encode(['name' => 'test', 'email' => 'test@example.com', 'age' => 25]),
            $request->getBody()->getContents()
        );
    }
}