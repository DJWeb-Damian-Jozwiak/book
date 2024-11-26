<?php

declare(strict_types=1);

namespace DJWeb\Framework\View\Inertia;

use DJWeb\Framework\Config\Config;
use DJWeb\Framework\Http\JsonResponse;
use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Http\Stream;
use DJWeb\Framework\View\ViewManager;
use Psr\Http\Message\ResponseInterface;

class ResponseFactory
{
    public function createResponse(array $page): ResponseInterface
    {
        $response = new JsonResponse($page);
        $response = $response->withHeader('Vary', ['Accept', 'X-Requested-With'])
            ->withHeader('X-Inertia', 'true');

        if ($this->isInertiaRequest() || $this->isXmlHttpRequest()) {
            return $response;
        }

        $html = new ViewManager()->build(Config::get('views.default'))
            ->render(Inertia::getRootView(), ['page' => $page]);

        return $response
            ->withHeader('Content-Type', 'text/html; charset=utf-8')
            ->withHeader('X-Inertia', 'true')
            ->withHeader('Vary', 'X-Inertia')
            ->withContent($html);
    }

    private function isInertiaRequest(): bool
    {
        return isset($_SERVER['HTTP_X_INERTIA']) && $_SERVER['HTTP_X_INERTIA'] === 'true';
    }

    private function isXmlHttpRequest(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }
}
