<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RedirectMiddleware implements MiddlewareInterface
{
    /**
     * @var callable
     */
    private $exitCallback;

    public function __construct(?callable $exitCallback = null)
    {
        $this->exitCallback = $exitCallback ?? exit(...);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        if ($response->hasHeader('Location')) {
            $location = $response->getHeaderLine('Location');
            $statusCode = $response->getStatusCode();

            if ($statusCode < 300 || $statusCode >= 400) {
                $statusCode = 302;
            }

            header('Location: ' . $location, true, $statusCode);
            $callback = $this->exitCallback;
            $callback($statusCode);
        }

        return $response;
    }
}
