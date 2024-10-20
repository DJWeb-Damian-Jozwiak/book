<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console\Commands;

use DJWeb\Framework\Console\Attributes\AsCommand;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\DBAL\Models\Attributes\FakeAs;
use ReflectionClass;
use ReflectionProperty;

#[AsCommand(name: 'make:factory')]
class MakeFactory extends MakeCommand
{
    protected string $fakerClass;

    public function __construct(ContainerContract $container)
    {
        parent::__construct($container);
        $class = $container->getBinding('app.faker_class') ?? FakeAs::class;
        $this->fakerClass = $class;
    }

    public function getModelClass(string $name): string
    {
        $modelClass = str_replace('Factory', '', $name);
        $modelClass = str_replace('.php', '', $modelClass);
        return '\\' . $this->rootNamespace() . 'Database\\Models\\' . $modelClass;
    }
    protected function getStub(): string
    {
        $dir = dirname(__DIR__, 3);

        return $dir . '/stubs/factory.stub';
    }

    protected function getDefaultNamespace(): string
    {
        return $this->rootNamespace() . 'Database\\Factories';
    }

    protected function buildClass(string $name): string
    {
        $modelClass = $this->getModelClass($name);

        $stub = parent::buildClass($name);

        $stub = str_replace('DummyModel', $modelClass, $stub);

        $reflection = new ReflectionClass($modelClass);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        $properties = array_filter(
            $properties,
            fn (ReflectionProperty $property) => (bool) $property->getAttributes($this->fakerClass)
        );

        $definitionContent = '';
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $fakerMethod = $this->guessFakerMethod($property);
            $definitionContent .= "            '{$propertyName}' => \$this->faker->{$fakerMethod}(),\n";
        }

        return str_replace('// DummyDefinition', $definitionContent, $stub);
    }

    protected function getPath(string $name): string
    {
        $name = str_replace('\\', '/', $name);

        return $this->container->getBinding(
            'app.factories_path'
        ) . '/' . $name;
    }

    private function guessFakerMethod(ReflectionProperty $property): string
    {
        $attributes = $property->getAttributes(FakeAs::class);
        $attribute = $attributes[0];
        /** @var FakeAs $fake */
        $fake = $attribute->newInstance();
        return $fake->method->value;
    }
}
