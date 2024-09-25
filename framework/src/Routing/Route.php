<?php

declare(strict_types=1);

namespace DJWeb\Framework\Routing;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Route
{
    /**
     * @var callable|array<int, string>
     */
    public private(set) mixed $handler;
    private readonly string $method;

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

    public function getMethod(): string
    {
        return $this->method;
    }

    public function execute(RequestInterface $request): ResponseInterface
    {
        /** @phpstan-ignore-next-line */
        return call_user_func($this->handler, $request);
    }
}
