<?php

namespace Tests\Container;

use DJWeb\Framework\Container\ReflectionResolver;
use DJWeb\Framework\Exceptions\Container\ContainerError;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;
use stdClass;
use Tests\Helpers\Database;
use Tests\Helpers\UserRepository;

class ReflectionResolverTest extends TestCase
{
    private ReflectionResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new ReflectionResolver();
    }

    public function testGetConstructorParameters()
    {
        $params = $this->resolver->getConstructorParameters(Database::class);
        $this->assertCount(3, $params);
        $this->assertContainsOnlyInstancesOf(ReflectionParameter::class, $params);
    }

    public function testGetConstructorParametersWithNoConstructor()
    {
        $params = $this->resolver->getConstructorParameters(stdClass::class);
        $this->assertEmpty($params);
    }

    public function testGetParameterType()
    {
        $params = $this->resolver->getConstructorParameters(Database::class);
        $params2 = $this->resolver->getConstructorParameters(UserRepository::class);
        $this->assertEquals('string', $this->resolver->getParameterType($params[0]));
        $this->assertEquals(Database::class, $this->resolver->getParameterType($params2[0]));
    }

    public function testHasDefaultValue()
    {
        $params = $this->resolver->getConstructorParameters(Database::class);

        $this->assertFalse($this->resolver->hasDefaultValue($params[0]));
        $this->assertTrue($this->resolver->hasDefaultValue($params[1]));
    }

    public function testGetDefaultValue()
    {
        $params = $this->resolver->getConstructorParameters(Database::class);

        $this->assertEquals('defaultuser', $this->resolver->getDefaultValue($params[1]));
    }

    public function testGetDefaultValueThrowsException()
    {
        $params = $this->resolver->getConstructorParameters(Database::class);

        $this->expectException(ContainerError::class);
        $this->resolver->getDefaultValue($params[0]);
    }

    public static function defaultValueProvider(): array
    {
        return [
            ['int', 0],
            ['float', 0.0],
            ['string', ''],
            ['bool', false],
            ['array', []],
            ['unknown', null],
        ];
    }


    #[DataProvider('defaultValueProvider')]
    public function testReflectionResolverMatch(string $type, mixed $expected): void
    {
        $result = $this->resolver->getDefaultValueForBuiltInType($type);
        $this->assertSame($expected, $result);
    }
}