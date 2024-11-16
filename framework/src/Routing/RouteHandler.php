<?php

declare(strict_types=1);

namespace DJWeb\Framework\Routing;

use Closure;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionMethod;

readonly class RouteHandler
{
    final public function __construct(
        private ?string $controller = null,
        private ?string $action = null,
        private ?Closure $callback = null
    ) {
    }

    /**
     * @param ServerRequestInterface $request
     * @param array<string, mixed> $boundParameters
     * @param ContainerContract $container
     *
     * @return ResponseInterface
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function dispatch(
        ServerRequestInterface $request,
        array $boundParameters,
        ContainerContract $container
    ): ResponseInterface
    {
        if ($this->callback) {
            return ($this->callback)($request);
        }
        $arguments = $this->getArguments($request, $boundParameters);
        $controller = $container->get($this->controller ?? '');
        $action = $this->action;
        return $controller->$action(...$arguments);
    }

    /**
     * @param ServerRequestInterface $request
     * @param array<string, mixed> $boundParameters
     *
     * @return array<int|string, mixed>
     *
     * @throws \ReflectionException
     */
    public function getArguments(ServerRequestInterface $request, array $boundParameters): array
    {
        $reflection = new ReflectionMethod($this->controller ?? '', $this->action ?? '');
        $parameters = $reflection->getParameters();
        $parameters = array_filter($parameters, static fn ($parameter) => (bool) $parameter->getType());
           $parameters = array_filter(
               $parameters,
               static fn ($parameter) => isset($boundParameters[$parameter->getName()])
           );
        $args = array_map(static fn ($parameter) => $boundParameters[$parameter->getName()], $parameters);
        return [
            $request,
            ...$args,
        ];
    }

    public function withNamespace(string $namespace): static
    {
        if(! $this->controller) {
            return $this;
        }
        return new static(
            $namespace . '\\' . $this->controller,
            $this->action,
            $this->callback
        );
    }
}
