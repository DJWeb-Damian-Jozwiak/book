<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Middleware;

use DJWeb\Framework\View\Inertia\Inertia;
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
        if (! $this->shouldIntercept($request)) {
            return $handler->handle($request);
        }
        Inertia::withRootView($this->rootView);
        $this->share();
        $response = $handler->handle($request);

        if ($response->getStatusCode() === 302) {
            return $response->withStatus(303);
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
        $session = Application::getInstance()->session;
        $errors = [];

        if ($session->has('errors')) {
            $errors = json_decode($session->get('errors'), true, flags: JSON_THROW_ON_ERROR);
            $session->remove('errors');
        }

        return [
            'errors' => $errors['errors'] ?? [],
            'message' => $errors['message'] ?? null,
        ];
    }

    private function isInertiaRequest(ServerRequestInterface $request): bool
    {
        return $request->hasHeader('X-Inertia');
    }

    private function shouldIntercept(ServerRequestInterface $request): bool
    {
        if (! $this->isInertiaRequest($request)) {
            return false;
        }

        $method = $request->getMethod();
        return in_array($method, ['GET', 'HEAD']);
    }
}
