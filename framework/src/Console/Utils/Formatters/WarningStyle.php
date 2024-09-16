<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console\Utils\Formatters;

use DJWeb\Framework\Console\Utils\BackgroundColor;
use DJWeb\Framework\Console\Utils\ForegroundColor;
use DJWeb\Framework\Console\Utils\Style;

final readonly class WarningStyle extends OutputFormatterStyle
{
    public function __construct()
    {
        parent::__construct(
            BackgroundColor::YELLOW,
            ForegroundColor::BLACK,
            Style::BOLD
        );
    }
}
