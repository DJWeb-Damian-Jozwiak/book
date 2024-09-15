<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Request extends BaseRequest implements RequestInterface
{
    /**
     * @var array<string, int|float|string|bool|null>
     */
    private array $queryParams;
    /**
     * @var array<string, int|float|string|bool|null>
     */
    private array $postParams;

    /**
     * @param array<string, int|float|string|bool|null>|null $queryParams
     * @param array<string, int|float|string|bool|null>|null $postParams
     */
    public function __construct(
        string $method,
        UriInterface $uri,
        StreamInterface $body,
        HeaderManager $headerManager,
        ?array $queryParams = null,
        ?array $postParams = null,
    ) {
        parent::__construct($method, $uri, $body, $headerManager);
        $this->postParams = $postParams ?? $_POST;
        $this->queryParams = $queryParams ?? $_GET;
        $this->uri = $this->uri->withQuery(
            http_build_query($this->queryParams)
        );
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->queryParams[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $this->postParams[$key] ?? $default;
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
        return isset($this->queryParams[$key])
            || isset($this->postParams[$key]);
    }

    /**
     * @return array<string, int|float|string|bool|null>
     */
    public function all(): array
    {
        return array_merge($this->queryParams, $this->postParams);
    }
}
