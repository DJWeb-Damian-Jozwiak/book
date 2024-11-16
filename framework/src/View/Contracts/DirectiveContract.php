<?php

declare(strict_types=1);

namespace DJWeb\Framework\View\Contracts;

interface DirectiveContract
{
    public string $name {get;}
    public function compile(string $content): string;
}
