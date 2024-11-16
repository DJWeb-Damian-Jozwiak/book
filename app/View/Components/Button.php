<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Enums\ButtonType;
use DJWeb\Framework\View\Component;

class Button extends Component
{
    public function __construct(
        public ButtonType $type = ButtonType::Primary,
        public ?string $size = null,
        public bool $outline = false,
        public bool $disabled = false,
        public ?string $class = '',
    ) {}

    protected function getView(): string
    {
        return 'components.button';
    }
}