<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console\Utils\Formatters;

use DJWeb\Framework\Console\Utils\BackgroundColor;
use DJWeb\Framework\Console\Utils\ForegroundColor;
use DJWeb\Framework\Console\Utils\Style;

final readonly class SuccessStyle extends OutputFormatterStyle
{
    public function __construct()
    {
        parent::__construct(
            BackgroundColor::GREEN,
            ForegroundColor::WHITE,
            Style::BOLD
        );
    }
}
