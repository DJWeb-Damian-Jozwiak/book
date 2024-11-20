<?php

declare(strict_types=1);

namespace DJWeb\Framework\ErrorHandling\Renderers;

use DJWeb\Framework\ErrorHandling\Backtrace;
use DJWeb\Framework\Exceptions\Container\NotFoundError;
use DJWeb\Framework\View\Inertia\Inertia;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class WebRenderer
{
    public function __construct(
        private bool $debug = false,
        private Backtrace $backtrace = new Backtrace()
    ) {
    }

    public function render(Throwable $exception): ResponseInterface
    {
        $statusCode = $this->getStatusCode($exception);
        http_response_code($statusCode);

        if ($this->debug) {
            return $this->renderDebugView($exception);
        }

        return $this->renderProductionView($exception);
    }

    private function renderDebugView(Throwable $exception): ResponseInterface
    {
        $trace = $this->backtrace->generate($exception);
        return Inertia::render('Pages/Errors/Debug.vue', [
            'exception' => [
                'class' => $exception::class,
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ],
            'backtrace' => $trace->frames,
        ]);
    }
    private function renderProductionView(Throwable $exception): ResponseInterface
    {
        $statusCode = $this->getStatusCode($exception);
        $msg = match ($statusCode) {
            404 => '<h1>404 Not Found</h1><p>The requested page could not be found.</p>',
            default => '<h1>500 Internal Server Error</h1><p>An error occurred. Please try again later.</p>'
        };
        return Inertia::render('Pages/Errors/Production.vue', [
            'title' => $msg,
            'status' => $statusCode,
        ]);
    }

    private function getStatusCode(Throwable $exception): int
    {
        return match (true) {
            $exception instanceof NotFoundError => 404,
            default => 500
        };
    }

}
