<?php

declare(strict_types=1);

namespace DJWeb\Framework\View\Directives;

class SectionDirective extends Directive
{

    public string $name {
        get => 'section';
    }

    public function compile(string $content): string
    {
        $content = $this->compilePattern(
            '/\@section\([\'"](.*?)[\'"]\)/',
            $content,
            fn($matches) => "<?php \$this->section('{$matches[1]}'); ?>"
        );



        return $this->compilePattern(
            '/\@endsection/',
            $content,
            fn() => "<?php \$this->endSection(); ?>"
        );
    }
}