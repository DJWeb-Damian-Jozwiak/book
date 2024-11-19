<?php

declare(strict_types=1);

namespace DJWeb\Framework\View\Directives;

class InertiaHeadDirective extends Directive
{
    public function compile(string $content): string
    {
        return $this->compilePattern(
            '/\@inertiaHead/',
            $content,
            static function () {
                return '<?php echo new \\DJWeb\\Framework\\View\\Inertia\\Inertia()->head(); ?>';
            }
        );
    }
}
