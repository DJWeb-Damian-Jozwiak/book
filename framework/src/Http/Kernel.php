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
        return $this->middlewareStack->handle($request);
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
