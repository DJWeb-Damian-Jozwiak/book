<?php

declare(strict_types=1);

namespace DJWeb\Framework\Routing;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Route
{
    public readonly string $method;
    /**
     * @var callable|array<int, string>
     */
    protected $handler;

    /**
     * @param string $path
     * @param string $method
     * @param callable|array<int, string> $handler
     * @param string|null $name
     */
    public function __construct(
        public readonly string $path,
        string $method,
        callable|array $handler,
        public readonly ?string $name = null
    ) {
        if (is_array(
            $handler
        ) && (! isset($handler[0], $handler[1]) || ! is_string(
            $handler[0]
        ) || ! is_string($handler[1]))) {
            throw new \InvalidArgumentException(
                'If $handler is an array, it must contain two string elements: [controllerClass, methodName]'
            );
        }
        $this->method = strtoupper($method);
        $this->handler = $handler;
    }

    /**
     * @return callable|array<int, string>
     */
    public function getHandler(): callable|array
    {
        return $this->handler;
    }

    /**
     * Check if this route matches the given request.
     *
     * @param RequestInterface $request The incoming request
     *
     * @return bool True if the route matches, false otherwise
     */
    public function matches(RequestInterface $request): bool
    {
        return $this->matchesPath($request->getUri()->getPath()) &&
            $this->matchesMethod($request->getMethod());
    }

    public function execute(RequestInterface $request): ResponseInterface
    {
        /** @phpstan-ignore-next-line */
        return call_user_func($this->handler, $request);
    }

    /**
     * Check if the given path matches this route's path.
     *
     * @param string $path The path to check
     *
     * @return bool True if the path matches, false otherwise
     */
    protected function matchesPath(string $path): bool
    {
        $trimmed = rtrim($this->path, '/');
        $trimmed2 = rtrim($path, '/');
        return $trimmed === $trimmed2;
    }

    /**
     * Check if the given method matches this route's method.
     *
     * @param string $method The HTTP method to check
     *
     * @return bool True if the method matches, false otherwise
     */
    private function matchesMethod(string $method): bool
    {
        return $this->method === strtoupper($method);
    }
}
