<?php

namespace App\View\Components;

use DJWeb\Framework\View\Component;

class Card extends Component
{
    public function __construct(
        public ?string $header = null,
        public ?string $footer = null,
        public ?string $class = '',
    ) {}

    protected function getView(): string
    {
        return 'components.card';
    }
}