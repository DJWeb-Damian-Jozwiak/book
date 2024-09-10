<?php

namespace Tests\Container;

use DJWeb\Framework\Container\Container;
use DJWeb\Framework\Container\Definition;
use DJWeb\Framework\Container\ServiceProvider;
use PHPUnit\Framework\TestCase;

class ServiceProviderTest extends TestCase
{
    public function testAddProvider(): void
    {
        $aggregate = new ServiceProvider();
        $provider = new ServiceProvider();

        $result = $aggregate->addProvider($provider);

        $this->assertSame($aggregate, $result);
        $this->assertCount(1, $aggregate->getProviders());
        $this->assertSame($provider, $aggregate->getProviders()[0]);
    }

    public function testGetDefinitions(): void
    {
        $aggregate = new ServiceProvider();

        $provider1 = new ServiceProvider();
        $provider1->addDefinition(new Definition('service1', 'Service1Class'));
        $provider1->addDefinition(new Definition('service2', 'Service2Class'));

        $provider2 = new ServiceProvider();
        $provider2->addDefinition(new Definition('service3', 'Service3Class'));

        $aggregate->addProvider($provider1);
        $aggregate->addProvider($provider2);


        $definitions = $aggregate->getDefinitions();
        $definitions = array_values($definitions);

        $this->assertCount(3, $definitions);
        $this->assertInstanceOf(Definition::class, $definitions[0]);
        $this->assertEquals('service1', $definitions[0]->id);
        $this->assertEquals('service3', $definitions[2]->id);
    }

    public function testRegister(): void
    {
        $container = new Container();
        $aggregate = new ServiceProvider();

        $provider1 = new ServiceProvider();
        $provider1->addDefinition(new Definition('service1', 'Service1Class'));

        $provider2 = new ServiceProvider();
        $provider2->addDefinition(new Definition('service2', 'Service2Class'));

        $aggregate->addProvider($provider1);
        $aggregate->addProvider($provider2);

        $aggregate->register($container);
        $this->assertTrue($container->has('service1'));
        $this->assertTrue($container->has('service2'));
    }

//
    public function testGetProviders(): void
    {
        $aggregate = new ServiceProvider();
        $provider1 = new ServiceProvider();
        $provider2 = new ServiceProvider();

        $aggregate->addProvider($provider1);
        $aggregate->addProvider($provider2);

        $providers = $aggregate->getProviders();

        $this->assertCount(2, $providers);
        $this->assertSame($provider1, $providers[0]);
        $this->assertSame($provider2, $providers[1]);
    }
}