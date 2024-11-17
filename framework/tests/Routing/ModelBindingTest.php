<?php

namespace Tests\Routing;

use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\DBAL\Contracts\Query\SelectQueryBuilderContract;
use DJWeb\Framework\Web\Application;
use Tests\BaseTestCase;
use Tests\Helpers\Casts\Status;

class ModelBindingTest extends BaseTestCase
{
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
        $builder = $this->createMock(SelectQueryBuilderContract::class);
        $builder->expects($this->once())->method('table')->willReturnSelf();
        $builder->expects($this->once())->method('where')->willReturnSelf();
        $builder->expects($this->once())->method('first')
            ->willReturn(['id' => 1, 'status' => Status::published->value]);
        $app->set(SelectQueryBuilderContract::class, $builder);
        $app->loadRoutes('\\Tests\\Helpers', dirname(__DIR__) . '/Helpers');

        $json = $app->handle();
        $this->assertJson('{"id":1,"status":"published"}', $json->getBody()->getContents());
    }
}