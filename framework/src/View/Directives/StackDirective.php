<?php

declare(strict_types=1);

namespace DJWeb\Framework\View\Directives;

class StackDirective extends Directive
{
    public string $name {
        get => 'stack';
    }

    public function compile(string $content): string
    {
        $content = $this->compilePattern(
            '/\@push\([\'"](.*?)[\'"]\)(.*?)\@endpush/s',
            $content,
            fn ($matches) => "<?php \$this->pushToStack('{$matches[1]}'".
                ", (function() { ob_start(); ?>{$matches[2]}<?php return ob_get_clean(); })(); ?>"
        );

        // Handle @stack
        return $this->compilePattern(
            '/\@stack\([\'"](.*?)[\'"]\)/',
            $content,
            fn ($matches) => "<?php echo \$this->renderStack('{$matches[1]}'); ?>"
        );
    }
}
