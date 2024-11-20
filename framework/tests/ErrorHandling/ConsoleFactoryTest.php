<?php

namespace ErrorHandling;

use DJWeb\Framework\Base\Application;
use DJWeb\Framework\Console\Output\Contacts\OutputContract;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\ErrorHandling\Renderers\ConsoleRenderer;
use DJWeb\Framework\ErrorHandling\Renderers\Partials\ConsoleRendererFactory;
use PHPUnit\Framework\TestCase;
use Tests\BaseTestCase;

class ConsoleFactoryTest extends BaseTestCase
{
    public function testCreateRenderer()
    {
        $container = Application::getInstance() ;
        $output = $this->createMock(OutputContract::class);
        $container->set(OutputContract::class, $output);
        $renderer = new ConsoleRendererFactory($container)->create();
        $this->assertInstanceOf(ConsoleRenderer::class, $renderer);
    }
}