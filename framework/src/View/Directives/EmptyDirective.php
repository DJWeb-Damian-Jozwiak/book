<?php

declare(strict_types=1);

namespace DJWeb\Framework\View\Directives;

class EmptyDirective extends Directive
{
    public string $name {
        get => 'empty';
    }

    public function compile(string $content): string
    {
        $content = $this->compilePattern(
            '/\@empty\s*\((.*?)\)/',
            $content,
            static fn ($matches) => "<?php if(empty({$matches[1]})): ?>"
        );

        return $this->compilePattern(
            '/\@endempty/',
            $content,
            static fn () => '<?php endif; ?>'
        );
    }
}
