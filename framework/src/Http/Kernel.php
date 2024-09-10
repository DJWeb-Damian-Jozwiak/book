<?php

namespace DJWeb\Framework\Http;

use DJWeb\Framework\Container\Contracts\ContainerInterface;
use DJWeb\Framework\Routing\Router;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Kernel
{
    private Router $router;

    public function __construct(ContainerInterface $container)
    {
        $this->router = $container->get(Router::class);
    }

    public function handle(RequestInterface $request): ResponseInterface
    {
        return $this->router->dispatch($request);
    }

    public function withRoutes(callable $callback): self
    {
        $callback($this->router);
        return $this;
    }
}