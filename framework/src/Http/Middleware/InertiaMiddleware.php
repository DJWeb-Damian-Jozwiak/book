<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Middleware;

use DJWeb\Framework\Http\JsonResponse;
use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Log\Log;
use DJWeb\Framework\View\Inertia\Inertia;
use DJWeb\Framework\View\Inertia\ResponseFactory;
use DJWeb\Framework\Web\Application;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class InertiaMiddleware implements MiddlewareInterface
{
    protected string $rootView = 'inertia.blade.php';

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->isInertiaRequest($request)) {
            return $handler->handle($request);
        }

        Inertia::withRootView($this->rootView);
        $this->share();

        $response = $handler->handle($request);

        // If this is a GET request and the response is a redirect,
        // We need to convert it to a 409 Conflict response
        if ($request->getMethod() === 'GET' && $this->isRedirectResponse($response)) {
            $location = $response->getHeaderLine('Location');

            return new Response()
                ->withStatus(409)
                ->withHeader('X-Inertia-Location', $location);
        }

        // For JSON responses, ensure they're proper Inertia responses
        if ($response instanceof JsonResponse) {
            $data = json_decode((string)$response->getBody(), true);

            if (!isset($data['component']) || !isset($data['props'])) {
                throw new \RuntimeException('Invalid Inertia response: missing required fields');
            }

            // Add Vary header
            $response = $response->withHeader('Vary', 'X-Inertia');
        }

        return $response;
    }

    public function share(): void
    {
        $data = $this->getShareData();
        foreach ($data as $key => $value) {
            Inertia::share($key, $value);
        }
    }

    protected function getShareData(): array
    {
        return [];
    }

    private function isInertiaRequest(ServerRequestInterface $request): bool
    {
        return $request->hasHeader('X-Inertia');
    }

    private function isRedirectResponse(ResponseInterface $response): bool
    {
        return in_array($response->getStatusCode(), [301, 302, 303, 307, 308]);
    }
}