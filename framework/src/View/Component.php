<?php

declare(strict_types=1);

namespace DJWeb\Framework\View;

use DJWeb\Framework\View\Engines\BladeAdapter;
use ReflectionClass;
use ReflectionProperty;

abstract class Component
{
    protected array $data = [];
    protected array $slots = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    abstract protected function getTemplatePath(): string;

    public function render(): string
    {
        $template = file_get_contents($this->getTemplatePath());
        return $this->interpolateTemplate($template);
    }

    protected function interpolateTemplate(string $template): string
    {
        // Implementation of template interpolation with slots and data
        return $template;
    }
}
