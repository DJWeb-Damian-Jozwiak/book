<?php

namespace Tests\Container;

use DJWeb\Framework\Container\Definition;
use PHPUnit\Framework\TestCase;

class DefinitionTest extends TestCase
{
    public function testConstructor()
    {
        $definition = new Definition('service_id', 'ServiceClass');
        $this->assertEquals('service_id', $definition->id);
        $this->assertEquals('ServiceClass', $definition->className);
    }

    public function testAddArgument()
    {
        $definition = new Definition('service_id', 'ServiceClass');
        $definition->addArgument('arg1')->addArgument('arg2');
        $this->assertEquals(['arg1', 'arg2'], $definition->getArguments());
    }

    public function testAddMethodCall()
    {
        $definition = new Definition('service_id', 'ServiceClass');
        $definition->addMethodCall('method1', ['arg1'])
            ->addMethodCall('method2', ['arg2', 'arg3']);
        $expectedCalls = [
            ['method1', ['arg1']],
            ['method2', ['arg2', 'arg3']]
        ];
        $this->assertEquals($expectedCalls, $definition->getMethodCalls());
    }

    public function testSetShared()
    {
        $definition = new Definition('service_id', 'ServiceClass');
        $this->assertTrue($definition->shared); // Default value
        $definition->shared = false;
        $this->assertFalse($definition->shared);
    }

    public function testSetFactory()
    {
        $definition = new Definition('service_id', 'ServiceClass');
        $factory = fn() => new \stdClass();
        $definition->factory = $factory;
        $this->assertSame($factory, $definition->factory);
    }
}