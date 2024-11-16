<?php

declare(strict_types=1);

namespace DJWeb\Framework\View\Contracts;

interface AssetManagerContract
{
    public function push(string $stack, string $content): void;
public function render(string $stack): string;

}
