<?php

declare(strict_types=1);

namespace DJWeb\Framework\ErrorHandling\Handlers;

use DJWeb\Framework\ErrorHandling\BaseHandler;
use DJWeb\Framework\ErrorHandling\Renderers\WebRenderer;
use Throwable;

class WebHandler extends BaseHandler
{
    public function __construct(private WebRenderer $renderer) {
    }

    public function handleException(Throwable $exception): void
    {
        try {
            echo $this->renderer->render($exception)->getBody()->getContents();
        } catch (Throwable) {
            echo 'Critical error occurred. Please check error logs.';
        }
    }
}
