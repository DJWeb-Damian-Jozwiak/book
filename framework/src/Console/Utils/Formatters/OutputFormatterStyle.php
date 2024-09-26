<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console\Utils\Formatters;

use DJWeb\Framework\Console\Utils\BackgroundColor;
use DJWeb\Framework\Console\Utils\ForegroundColor;
use DJWeb\Framework\Console\Utils\Style;

readonly class OutputFormatterStyle
{
    public function __construct(
        private ?BackgroundColor $backgroundColor = null,
        private ?ForegroundColor $foregroundColor = null,
        private ?Style $style = null
    ) {
    }

    public function apply(string $text): string
    {
        $setCodes = array_filter(
            [$this->style, $this->foregroundColor, $this->backgroundColor]
        );
        $setCodes = array_map(
            static fn (mixed $enum) => $enum->value,
            $setCodes
        );
        $imploded = implode(';', $setCodes);
        return $imploded ? "\033[{$imploded}m{$text}\033[0m" : $text;
    }
}
