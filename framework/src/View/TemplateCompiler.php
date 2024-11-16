<?php

declare(strict_types=1);

namespace DJWeb\Framework\View;

use DJWeb\Framework\View\Contracts\DirectiveContract;

class TemplateCompiler
{
    /**
     * @param array<int, DirectiveContract> $directives
     */
    public function __construct(
        public private(set) array $directives = []
    ) {
    }

    public function compile(string $content): string
    {
        // Najpierw kompilujemy komentarze, żeby nie interferowały z innymi dyrektywami
        $content = $this->compileComments($content);

        // Kompilujemy wyrażenia
        $content = $this->compileEchos($content);

        // Kompilujemy pozostałe dyrektywy
        array_walk(
            array: $this->directives,
            callback: function (DirectiveContract $directive) use (&$content) {
                $content = $directive->compile($content);
            }
        );

        return $content;
    }


    private function compileComments(string $content): string
    {
        return preg_replace('/\{\{--(.*?)--\}\}/s', '<?php /* $1 */ ?>', $content);
    }

    private function compileEchos(string $content): string
    {
        // Raw echo {!! $var !!}
        $content = preg_replace('/\{!!(.*?)!!\}/', '<?php echo $1; ?>', $content);

        // Escaped echo {{ $var }}
        return preg_replace('/\{\{(.*?)\}\}/', '<?php echo htmlspecialchars($1, ENT_QUOTES, \'UTF-8\'); ?>', $content);
    }

    public function addDirective(DirectiveContract $directive): self
    {
        $this->directives[] = $directive;
        return $this;
    }
}
