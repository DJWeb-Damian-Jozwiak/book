<?php

declare(strict_types=1);

namespace DJWeb\Framework\View\Contracts;

interface ComponentContract
{
    public function render(): string;

}
