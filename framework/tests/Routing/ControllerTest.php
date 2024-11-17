<?php

namespace Tests\Routing;

use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Query\Builders\DeleteQueryBuilder;
use DJWeb\Framework\DBAL\Query\Builders\InsertQueryBuilder;
use DJWeb\Framework\DBAL\Query\Builders\SelectQueryBuilder;
use DJWeb\Framework\DBAL\Query\Builders\UpdateQueryBuilder;
use DJWeb\Framework\Web\Application;
use Tests\BaseTestCase;
class ControllerTest extends BaseTestCase
{
    public function setUp(): void
    {
        Application::withInstance(null);
//        $this->mockConnection = $this->createMock(ConnectionContract::class);
//        Application::getInstance()->set(
//            InsertQueryBuilder::class,
//            new InsertQueryBuilder($this->mockConnection)
//        );
//        Application::getInstance()->set(
//            UpdateQueryBuilder::class,
//            new UpdateQueryBuilder($this->mockConnection)
//        );
//        Application::getInstance()->set(
//            DeleteQueryBuilder::class,
//            new DeleteQueryBuilder($this->mockConnection)
//        );
//        Application::getInstance()->set(
//            SelectQueryBuilder::class,
//            new SelectQueryBuilder($this->mockConnection)
//        );
    }
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

    public function testDispatchToControllerWithModel(): void
    {
        $_SERVER = ['SERVER_NAME' => 'example.com', 'SERVER_PORT' => '443'];
        $_SERVER['REQUEST_URI'] = '/test-with-model/post/1';
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
        //$this->assertEquals('abcde', $response->getBody()->getContents());
    }
}