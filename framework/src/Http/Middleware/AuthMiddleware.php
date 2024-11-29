<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Middleware;

use DJWeb\Framework\Auth\Auth;
use DJWeb\Framework\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

readonly class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(private ?string $redirectTo = '/login') {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (Auth::guest()) {
            return new Response()->redirect($this->redirectTo);

        }
        return $handler->handle($request);
    }

}
