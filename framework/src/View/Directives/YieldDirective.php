<?php

declare(strict_types=1);

namespace DJWeb\Framework\View\Directives;

class YieldDirective extends Directive
{
    public string $name {
        get => 'yield';
    }

    public function compile(string $content): string
    {
        return $this->compilePattern(
            '/\@yield\([\'"](.*?)[\'"]\)/',
            $content,
            fn ($matches) => "<?php echo \$this->yield('{$matches[1]}'); ?>"
        );
    }
}
