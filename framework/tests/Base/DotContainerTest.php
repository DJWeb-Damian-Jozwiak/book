<?php

namespace Tests\Base;

use ArrayObject;
use DJWeb\Framework\Base\DotContainer;
use PHPUnit\Framework\TestCase;

class DotContainerTest extends TestCase
{
    public function testSetAndGetWithDotNotation()
    {
        $container = new DotContainer();

        $container->set('foo.bar.baz', 'value');
        $this->assertEquals('value', $container->get('foo.bar.baz'));

        $container->set('foo.bar.qux', 123);
        $this->assertEquals(123, $container->get('foo.bar.qux'));
    }

    public function testGetWithDefaultValue()
    {
        $container = new DotContainer();

        $this->assertEquals('default', $container->get('non.existent.key', 'default'));
    }

    public function testNestedArrayObjectIsCreated()
    {
        $container = new DotContainer();

        $container->set('foo.bar.baz', 'value');
        $this->assertInstanceOf(ArrayObject::class, $container['foo']);
        $this->assertInstanceOf(ArrayObject::class, $container['foo']['bar']);
    }
}