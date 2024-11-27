<?php

declare(strict_types=1);

namespace DJWeb\Framework\View\Inertia;

use DJWeb\Framework\Config\Config;
use DJWeb\Framework\Http\JsonResponse;
use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Http\Stream;
use DJWeb\Framework\Log\Log;
use DJWeb\Framework\View\ViewManager;
use Psr\Http\Message\ResponseInterface;
class ResponseFactory
{
    public function createResponse(array $page): ResponseInterface
    {
        // Jeśli to visit request (nie XHR) - zwracamy HTML
        if (!$this->isXmlHttpRequest()) {
            return $this->htmlResponse($page);
        }

        // Jeśli to Inertia i chce HTML (czyli nawigacja przez link) - zwracamy 409
        if ($this->isInertiaRequest() && $this->wantsHtml()) {
            return $this->locationResponse($page['url'] ?? '/');
        }

        // W innych przypadkach zwracamy JSON
        return $this->jsonResponse($page);
    }

    private function htmlResponse(array $page): ResponseInterface
    {
        return new Response()
            ->withHeader('Content-Type', 'text/html; charset=utf-8')
            ->withContent(
                new ViewManager()
                    ->build(Config::get('views.default'))
                    ->render(Inertia::getRootView(), ['page' => $page])
            );
    }

    private function locationResponse(string $url): ResponseInterface
    {
        return new Response()
            ->withStatus(409)
            ->withHeader('X-Inertia-Location', $url);
    }

    private function jsonResponse(array $page): ResponseInterface
    {
        return (new JsonResponse($page))
            ->withHeader('X-Inertia', 'true')
            ->withHeader('Vary', 'Accept');
    }

    private function isInertiaRequest(): bool
    {
        return isset($_SERVER['HTTP_X_INERTIA']);
    }

    private function isXmlHttpRequest(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    private function wantsHtml(): bool
    {
        return isset($_SERVER['HTTP_ACCEPT'])
            && str_contains($_SERVER['HTTP_ACCEPT'], 'text/html');
    }
}