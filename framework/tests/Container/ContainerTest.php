<?php

namespace Tests\Container;

use DJWeb\Framework\Container\Container;
use DJWeb\Framework\Container\Contracts\ServiceProviderContract;
use DJWeb\Framework\Container\Definition;
use PHPUnit\Framework\TestCase;
use Tests\Helpers\Database;
use Tests\Helpers\UserRepository;

class ContainerTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $this->container = new Container();
    }

    public function testAddDefinition(): void
    {
        $definition = new Definition('service', Database::class);
        $this->container->addDefinition($definition);

        $this->assertTrue($this->container->has('service'));
        $this->assertInstanceOf(Definition::class, $this->container->get('service'));
    }

    public function testRegisterServiceProvider(): void
    {
        $provider = $this->createMock(ServiceProviderContract::class);
        $provider->expects($this->once())
            ->method('register')
            ->with($this->container);

        $result = $this->container->register($provider);

        $this->assertSame($this->container, $result);
    }

    public function testAutowireSimpleClass(): void
    {
        $instance = $this->container->get(Database::class);
        $this->assertInstanceOf(Database::class, $instance);
    }

    public function testAutowireWithDependencies(): void
    {
        $instance = $this->container->get(UserRepository::class);
        $this->assertInstanceOf(UserRepository::class, $instance);
        $this->assertInstanceOf(Database::class, $instance->database);
    }
}