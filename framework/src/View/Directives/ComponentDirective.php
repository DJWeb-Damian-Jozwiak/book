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
            function ($matches) {
                $slotName = $matches[1];
                $slotContent = $this->compileComponents($matches[2]);
                return "<?php \$__current_component = \$__component; ob_start(); ?>{$slotContent}<?php \$__component->withNamedSlot('{$slotName}', trim(ob_get_clean())); ?>";
            }
        );

        // Kompilujemy główny komponent i zagnieżdżone
        return $this->compileComponents($content);
    }

    private function compileComponents(string $content): string
    {
        return preg_replace_callback(
            '/\<x-([^>]+)(?:\s([^>]*))?\>(.*?)\<\/x-\1\>/s',
            function ($matches) {
                static $counter = 0;
                $counter++;

                $componentName = $this->formatComponentName($matches[1]);
                $attributes = $this->parseAttributes($matches[2] ?? '');
                $slot = $matches[3] ?? '';

                $varName = "\$__component_{$counter}";

                // Rekurencyjnie kompilujemy zagnieżdżone komponenty w slocie
                $compiledSlot = $this->compileComponents($slot);

                return "<?php 
                    \$__prev_component = \$__component ?? null;
                    {$varName} = new \\App\\View\\Components\\{$componentName}({$attributes}); 
                    \$__component = {$varName};
                    ob_start(); 
                    ?>{$compiledSlot}<?php 
                    \$__component->withSlot(trim(ob_get_clean()));
                    echo \$__component->render();
                    \$__component = \$__prev_component;
                ?>";
            },
            $content
        );
    }

    private function parseAttributes(string $attributesString): string
    {
        if (! $attributesString) {
            return '';
        }

        preg_match_all('/(\w+)=[\'"](.*?)[\'"]/', $attributesString, $matches, PREG_SET_ORDER);

        $attributes = [];
        foreach ($matches as $match) {
            $name = $match[1];
            $value = $match[2];

            // Konwersja stringów na odpowiednie typy
            if ($value === 'true') {
                $value = true;
            } elseif ($value === 'false') {
                $value = false;
            }

            $attributes[$name] = $value;
        }

        return implode(', ', array_map(
            static function ($key, $value) {
                if (is_bool($value)) {
                    return "{$key}: " . ($value ? 'true' : 'false');
                }
                return "{$key}: '{$value}'";
            },
            array_keys($attributes),
            $attributes
        ));
    }

    private function formatComponentName(string $name): string
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $name)));
    }
}
