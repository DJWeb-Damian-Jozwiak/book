<?php

declare(strict_types=1);

namespace DJWeb\Framework\ErrorHandling\Handlers;

use DJWeb\Framework\ErrorHandling\BaseHandler;
use DJWeb\Framework\ErrorHandling\Renderers\WebRenderer;
use Throwable;

class WebHandler extends BaseHandler
{
    public function __construct(private WebRenderer $renderer, private \Closure $output)
    {
    }

    public function handleException(Throwable $exception): void
    {
        try {
            ($this->output)(
                $this->renderer->render($exception)->getBody()->getContents()
            );
        } catch (Throwable) {
            ($this->output)('Critical error occurred. Please check error logs.');
        }
    }
}
