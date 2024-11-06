<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http;

use DJWeb\Framework\Routing\Router;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MiddlewareStack implements RequestHandlerInterface
{
    /**
     * @var array<int, MiddlewareInterface>
     */
    private array $middleware = [];
    private int $currentIndex = 0;
    public function __construct(private Router $router)
    {
    }

    public function add(MiddlewareInterface $middleware): self
    {
        $this->middleware[] = $middleware;
        return $this;
    }

    public function remove(MiddlewareInterface $middleware): self
    {
        $this->middleware = array_filter(
            $this->middleware,
            static fn (MiddlewareInterface $item) => $item::class !== $middleware::class
        );
        return $this;
    }

    public function handle(ServerRequestInterface $request): \Psr\Http\Message\ResponseInterface
    {
        if ($this->currentIndex === count($this->middleware) && $this->currentIndex === 0) {
            return $this->router->dispatch($request);
        }

        $middleware = $this->middleware[$this->currentIndex];
        $this->currentIndex++;

        return $middleware->process($request, $this);
    }
}
