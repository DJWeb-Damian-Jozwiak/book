<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http;

use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Kernel implements RequestHandlerInterface
{
    public private(set) Router $router;
    private MiddlewareStack $middlewareStack;

    public function __construct(private(set) ContainerContract $container, private MiddlewareConfig $config)
    {
        $this->router = $container->get(Router::class);
        $this->middlewareStack = new MiddlewareStack($this->router);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->config->configure($this);
        $response = $this->middlewareStack->handle($request);
        $response = $this->handleHeaders($response);
        return $this->handleRedirects($response);
    }

    public function handleHeaders(ResponseInterface $response): ResponseInterface
    {
        http_response_code($response->getStatusCode());
        $headers = array_filter(
            $response->getHeaders(),
            fn($name) => $name !== 'Location',
            ARRAY_FILTER_USE_KEY
        );
        foreach ($headers as $name => $values) {
            header($name . ': ' . implode(', ', $values), false);
        }
        return $response;
    }

    public function handleRedirects(ResponseInterface $response, ?callable $callback = null): ResponseInterface
    {
        $callback ??= exit(...);
        if ($response->hasHeader('Location')) {
            $location = $response->getHeaderLine('Location');
            $statusCode = $response->getStatusCode();

            if ($statusCode < 300 || $statusCode >= 400) {
                $statusCode = 302;
            }

            header('Location: ' . $location, true, $statusCode);
            $callback($statusCode);
        }

        return $response;
    }

    /**
     * @param MiddlewareInterface|array<int, mixed> $middleware
     *
     * @return $this
     */
    public function withMiddleware(MiddlewareInterface|array $middleware): self
    {
        $items = is_array($middleware) ? $middleware : [$middleware];
        $items = array_filter($items, static fn (mixed $item) => $item instanceof MiddlewareInterface);
        array_walk($items, fn (MiddlewareInterface $item) => $this->middlewareStack->add($item));
        return $this;
    }

    /**
     * @param MiddlewareInterface|array<int, mixed> $middleware
     *
     * @return $this
     */
    public function withoutMiddleware(MiddlewareInterface|array $middleware): self
    {
        $items = is_array($middleware) ? $middleware : [$middleware];
        array_walk($items, fn (MiddlewareInterface $item) => $this->middlewareStack->remove($item));
        return $this;
    }

    public function withRoutes(callable $callback): self
    {
        $callback($this->router);
        return $this;
    }
}
