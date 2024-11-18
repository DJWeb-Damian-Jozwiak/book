<?php

declare(strict_types=1);

namespace DJWeb\Framework\View\Contracts;

interface DirectiveContract
{
    public function compile(string $content): string;
}
