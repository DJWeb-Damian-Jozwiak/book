<?php

namespace Tests\Container;

use DJWeb\Framework\Container\Autowire;
use DJWeb\Framework\Container\Container;
use DJWeb\Framework\Container\ReflectionResolver;
use PHPUnit\Framework\TestCase;
use stdClass;
use Tests\Helpers\ClassWithBuiltInTypes;
use Tests\Helpers\ClassWithDefaultValue;
use Tests\Helpers\ClassWithDependency;
use Tests\Helpers\ClassWithNullableParam;
use tests\Helpers\SimpleClass;

class AutowireTest extends TestCase
{
    private Container $container;
    private ReflectionResolver $reflectionResolver;
    private Autowire $autowire;

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->reflectionResolver = new ReflectionResolver();
        $this->autowire = new Autowire($this->container, $this->reflectionResolver);
    }

    public function testNoConstructor()
    {
        $result = $this->autowire->instantiate(stdClass::class);
        $this->assertInstanceOf(stdClass::class, $result);
    }

    public function testInstantiateWithContainerValue(): void
    {
        $this->container->set('param', 'container_value');
        $result = $this->autowire->instantiate(ClassWithDefaultValue::class);

        $this->assertEquals('container_value', $result->param);
    }

    public function testInstantiateWithDefaultValue(): void
    {
        $result = $this->autowire->instantiate(ClassWithDefaultValue::class);

        $this->assertEquals('default_value', $result->param);
    }

    public function testInstantiateWithNullableParameter(): void
    {
        $result = $this->autowire->instantiate(ClassWithNullableParam::class);

        $this->assertNull($result->param);
    }

    public function testInstantiateWithBuiltInTypes(): void
    {
        $result = $this->autowire->instantiate(ClassWithBuiltInTypes::class);

        $this->assertIsInt($result->intParam);
        $this->assertIsFloat($result->floatParam);
        $this->assertIsString($result->stringParam);
        $this->assertIsBool($result->boolParam);
        $this->assertIsArray($result->arrayParam);
    }

    public function testInstantiateWithDependencies(): void
    {
        $result = $this->autowire->instantiate(ClassWithDependency::class);

        $this->assertInstanceOf(ClassWithDependency::class, $result);
        $this->assertInstanceOf(SimpleClass::class, $result->dependency);
    }
}