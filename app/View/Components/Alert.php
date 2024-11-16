<?php

declare(strict_types=1);

namespace App\View\Components;

use DJWeb\Framework\View\Component;

class Alert extends Component
{
    public function __construct(
        public string $type = 'info',
        public bool $dismissible = false,
        public ?string $class = '',
    ) {}

    protected function getView(): string
    {
        return 'components.alert';
    }
}