<?php

declare(strict_types=1);

namespace DJWeb\Framework\View\Directives;

class ComponentDirective extends Directive
{

    public string $name {
        get => 'component';
    }


    public function compile(string $content): string
    {
        return $this->compilePattern(
            '/\@component\([\'"](.*?)[\'"](.*?)\)/',
            $content,
            function($matches) {
                $component = $matches[1];
                $params = $matches[2] ? $this->parseParams($matches[2]) : '';
                return "<?php echo \$this->renderComponent('{$component}', [{$params}]); ?>";
            }
        );
    }

    private function parseParams(string $params): string
    {
        return $params;
    }
}