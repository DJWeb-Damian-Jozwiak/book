<?php

declare(strict_types=1);

namespace DJWeb\Framework\Mail;

use DJWeb\Framework\View\ViewManager;

final readonly class Content
{
    public function __construct(
        public string $view,
        public array $data = [],
        private string $renderer = 'blade'
    ) {
    }

    public function render(): string
    {
        return $this->renderView($this->view, $this->data);
    }

    private function renderView(string $view, array $data): string
    {
        $renderer = new ViewManager()->build($this->renderer);
        return $renderer->render($view, $data);
    }
}
