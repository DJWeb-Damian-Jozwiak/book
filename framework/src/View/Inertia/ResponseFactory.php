<?php

declare(strict_types=1);

namespace DJWeb\Framework\View\Inertia;

use DJWeb\Framework\Config\Config;
use DJWeb\Framework\Http\Response;
use DJWeb\Framework\View\ViewManager;
use Psr\Http\Message\ResponseInterface;

class ResponseFactory
{
    public function createResponse(array $page): ResponseInterface
    {
        $response = new Response()
            ->withHeader('Vary', 'Accept')
            ->withHeader('X-Inertia', 'true');

        if ($this->isInertiaRequest()) {
            return $response->withJson($page);
        }

        $html = new ViewManager()->build(Config::get('views.default'))
            ->render(Inertia::getRootView(), ['page' => $page]);

        return $response
            ->withHeader('Content-Type', 'text/html; charset=utf-8')
            ->withContent($html);
    }

    private function isInertiaRequest(): bool
    {
        return isset($_SERVER['HTTP_X_INERTIA']) && $_SERVER['HTTP_X_INERTIA'] === 'true';
    }
}
