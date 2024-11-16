<?php

declare(strict_types=1);

namespace DJWeb\Framework\View;

use DJWeb\Framework\Config\Config;
use DJWeb\Framework\View\Contracts\RendererContract;
use DJWeb\Framework\View\Contracts\ViewContract;
use DJWeb\Framework\View\Engines\TwigRendererAdapter;

class ViewManager
{
    public protected(set) RendererContract $renderer;

    /**
     * @param string $template
     * @param array<string, mixed> $data
     * @return ViewContract
     */
    public function make(string $template, array $data = []): ViewContract
    {
        $view = new View($template, $this->renderer);
        foreach ($data as $key => $value) {
            $view->with($key, $value);
        }
        return $view;
    }

    public function withRenderer(RendererContract $renderer): self
    {
        $this->renderer = $renderer;
        return $this;
    }

    public function build(string $name): RendererContract
    {
        $this->renderer = match ($name) {
            'twig' => TwigRendererAdapter::buildDefault(),
            default => throw new \Exception("View engine $name not found"),
        };
        return $this->renderer;
    }
}