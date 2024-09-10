<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Request;

use InvalidArgumentException;

trait MethodTrait
{
    private string $method = '';
    /**
     * @var list<string>
     */
    private static array $validMethods = [
        'GET',
        'POST',
        'PUT',
        'DELETE',
        'PATCH',
        'HEAD',
        'OPTIONS',
        'CONNECT',
        'TRACE',
    ];

    public function getMethod(): string
    {
        return $this->method;
    }

    public function withMethod(string $method): self
    {
        $method = strtoupper($method);
        if (! in_array($method, self::$validMethods, true)) {
            throw new InvalidArgumentException("Invalid HTTP method: $method");
        }
        $clone = clone $this;
        $clone->method = $method;
        return $clone;
    }
}