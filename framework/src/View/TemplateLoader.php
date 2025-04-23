<?php

declare(strict_types=1);

namespace DJWeb\Framework\View;

class TemplateLoader
{
    public function __construct(public private(set) string $template_path)
    {
    }

    public function load(string $template): string
    {
        $path = $this->template_path . '/' . $template;
        if (! file_exists($path)) {
            throw new \RuntimeException("Template not found: {$path}");
        }
        /** @var string $content */
        $content = file_get_contents($path);
        return $content;
    }
}
