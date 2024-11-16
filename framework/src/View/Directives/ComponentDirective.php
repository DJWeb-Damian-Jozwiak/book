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
        // Slot-y nazwane (@slot('name'))
        $content = $this->compilePattern(
            '/\@slot\([\'"](.*?)[\'"]\)(.*?)\@endslot/s',
            $content,
            fn($matches) => "<?php \$__component->withNamedSlot('{$matches[1]}', '{$matches[2]}'); ?>"
        );

        // Komponent z atrybutami
        $content = $this->compilePattern(
            '/\<x-([^>]+)(?:\s([^>]*))?\>(.*?)\<\/x-\1\>/s',
            $content,
            function($matches) {
                $componentName = $this->formatComponentName($matches[1]);
                $attributes = $this->parseAttributes($matches[2] ?? '');
                $slot = $matches[3] ?? '';

                return "<?php 
                    \$__component = new \\App\\View\\Components\\{$componentName}({$attributes}); 
                    \$__component->withSlot('{$slot}');
                    echo \$__component->render();
                ?>";
            }
        );

        return $content;
    }

    private function parseAttributes(string $attributesString): string
    {
        preg_match_all('/(\w+)=[\'"](.*?)[\'"]/', $attributesString, $matches, PREG_SET_ORDER);

        $attributes = [];
        foreach ($matches as $match) {
            $attributes[$match[1]] = $match[2];
        }

        return implode(', ', array_map(
            fn($key, $value) => "$key: '$value'",
            array_keys($attributes),
            $attributes
        ));
    }

    private function formatComponentName(string $name): string
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $name)));
    }
}