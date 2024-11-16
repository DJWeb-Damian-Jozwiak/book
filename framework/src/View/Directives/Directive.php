<?php

declare(strict_types=1);

namespace DJWeb\Framework\View\Directives;

use DJWeb\Framework\View\Contracts\DirectiveContract;

abstract class Directive implements DirectiveContract
{
    protected function compilePattern(string $pattern, string $content, callable $callback): string
    {
        return preg_replace_callback($pattern, $callback, $content);
    }

}
