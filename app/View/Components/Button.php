<?php

declare(strict_types=1);

namespace App\View\Components;

use DJWeb\Framework\View\Component;

class Button extends Component
{
    public function __construct(
        public string $type = 'primary',
        public ?string $size = null,
        public bool $outline = false,
        public bool $disabled = false,
        public ?string $class = '',
    ) {}

    protected function getView(): string
    {
        return 'components/button.blade.php';
    }
}