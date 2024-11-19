<?php

declare(strict_types=1);

namespace DJWeb\Framework\View\Directives;

class ViteDirective extends Directive
{
    public function compile(string $content): string
    {
        return $this->compilePattern(
            '/\@vite\s*\((.*?)\)/',
            $content,
            static function ($matches) {
                $assets = trim($matches[1]);
                return "<?php echo vite({$assets}); ?>";
            }
        );
    }
}
