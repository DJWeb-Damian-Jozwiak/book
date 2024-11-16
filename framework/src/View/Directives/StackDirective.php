<?php

declare(strict_types=1);

namespace DJWeb\Framework\View\Directives;

class StackDirective extends Directive
{
    public function compile(string $content): string
    {
        $content = $this->compilePattern(
            '/\@push\([\'"](.*?)[\'"]\)(.*?)\@endpush/s',
            $content,
            static fn ($matches) => <<<PHP
            <?php
\$this->pushToStack('{$matches[1]}' , (function() { ob_start(); ?>{$matches[2]}<?php return ob_get_clean(); })(); ?>
PHP
        );

        // Handle @stack
        return $this->compilePattern(
            '/\@stack\([\'"](.*?)[\'"]\)/',
            $content,
            fn ($matches) => "<?php echo \$this->renderStack('{$matches[1]}'); ?>"
        );
    }

    public function getName(): string
    {
        return 'stack';
    }
}
