<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Request\Psr7;

use Psr\Http\Message\ServerRequestInterface;

class Request extends ServerRequest implements ServerRequestInterface
{
    public function query(string $key, mixed $default = null): mixed
    {
        return $this->getQueryParams()[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $this->getParsedBody()[$key] ?? $default;
    }

    public function isGet(): bool
    {
        return $this->method === 'GET';
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->post($key) ?? $this->query($key) ?? $default;
    }

    public function has(string $key): bool
    {
        return $this->queryParamsManager->has($key)
            || $this->parsedBodyManager->has($key)
            || isset($this->postParams[$key]);
    }

    /**
     * @return array<string, int|float|string|bool|null>
     */
    public function all(): array
    {
        return array_merge($this->getQueryParams(), $this->getParsedBody());
    }
}
