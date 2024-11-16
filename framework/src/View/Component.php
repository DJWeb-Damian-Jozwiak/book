<?php

declare(strict_types=1);

namespace DJWeb\Framework\View;

use DJWeb\Framework\View\Engines\BladeAdapter;
use ReflectionClass;
use ReflectionProperty;

abstract class Component
{
    private ?array $publicProperties = null;
    private ?string $slot = null;
    private array $slots = [];

    abstract protected function getView(): string;

    public function withSlot(string $content): void
    {
        $this->slot = $content;
    }

    public function withNamedSlot(string $name, string $content): void
    {
        $this->slots[$name] = $content;
    }

    public function render(): string
    {
        $renderer = BladeAdapter::buildDefault();

        try{
            return $renderer->render(
                $this->getView(),
                array_merge(
                    $this->getPublicProperties(),
                    [
                        'slot' => $this->slot,
                        'slots' => $this->slots
                    ]
                )
            );
        } catch (\Throwable $e) {
            dd($e);
        }

    }

    private function getPublicProperties(): array
    {
        if ($this->publicProperties === null) {
            $reflection = new ReflectionClass($this);
            $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

            $this->publicProperties = [];
            foreach ($properties as $property) {
                if (!$property->isStatic()) {
                    $this->publicProperties[$property->getName()] = $property->getValue($this);
                }
            }
        }

        return $this->publicProperties;
    }

}