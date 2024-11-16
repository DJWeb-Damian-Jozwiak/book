<?php

declare(strict_types=1);

namespace DJWeb\Framework\View\Directives;

class ExtendDirective extends Directive
{
    public string $name {
        get => 'extends';
    }

    public function compile(string $content): string
    {
        return $this->compilePattern(
            '/\@extends\([\'"](.*?)[\'"]\)/',
            $content,
            fn ($matches) => "<?php \$this->extend('{$matches[1]}'); ?>"
        );
    }
}
