<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Request\Psr7;

use DJWeb\Framework\Http\HeaderManager;
use DJWeb\Framework\Http\Request\AttributesManager;
use DJWeb\Framework\Http\Request\ParsedBodyManager;
use DJWeb\Framework\Http\Request\QueryParamsManager;
use DJWeb\Framework\Http\Request\UploadedFilesManager;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class ServerRequest extends BaseRequest implements ServerRequestInterface
{
    /**
     * @param string $method
     * @param UriInterface $uri
     * @param StreamInterface $body
     * @param HeaderManager $headerManager
     * @param array<string, mixed> $serverParams
     * @param array<string, mixed> $cookieParams
     * @param QueryParamsManager $queryParamsManager
     * @param UploadedFilesManager $uploadedFilesManager
     * @param ParsedBodyManager $parsedBodyManager
     * @param AttributesManager $attributesManager
     */
    public function __construct(
        string $method,
        UriInterface $uri,
        StreamInterface $body,
        HeaderManager $headerManager,
        private ?array $serverParams,
        private array $cookieParams,
        protected QueryParamsManager $queryParamsManager,
        private UploadedFilesManager $uploadedFilesManager,
        protected ParsedBodyManager $parsedBodyManager,
        private AttributesManager $attributesManager
    ) {
        parent::__construct($method, $uri, $body, $headerManager);
    }

    /**
     * @return array<string, mixed>
     */
    public function getServerParams(): array
    {
        return $this->serverParams ?? [];
    }

    /**
     * @return array<string, mixed>
     */
    public function getCookieParams(): array
    {
        return $this->cookieParams;
    }

    /**
     * @param array<string, string|float|bool> $cookies
     *
     * @return $this
     */
    public function withCookieParams(array $cookies): static
    {
        $new = clone $this;
        $new->cookieParams = $cookies;
        return $new;
    }

    /**
     *  @return array<string, mixed>
     * /
     */
    public function getQueryParams(): array
    {
        return $this->queryParamsManager->queryParams ?? [];
    }

    /**
     * @param array<string, mixed> $query
     *
     * @return $this
     */
    public function withQueryParams(array $query): static
    {
        $new = clone $this;
        $new->queryParamsManager = $this->queryParamsManager->withQueryParams($query);
        return $new;
    }

    /**
     *  @return array<string, mixed>
     * /
     */
    public function getUploadedFiles(): array
    {
        return $this->uploadedFilesManager->uploadedFiles;
    }

    /**
     * @param array<string, mixed> $uploadedFiles
     *
     * @return $this
     */
    public function withUploadedFiles(array $uploadedFiles): static
    {
        $new = clone $this;
        $new->uploadedFilesManager = $this->uploadedFilesManager->withUploadedFiles($uploadedFiles);
        return $new;
    }

    /**
     * @return array<string, mixed>
     */
    public function getParsedBody(): array
    {
        return $this->parsedBodyManager->parsedBody ?? [];
    }

    /**
     * @param ?array<string, mixed> $data
     *
     * @return $this
     */
    public function withParsedBody($data): static
    {
        $new = clone $this;
        $new->parsedBodyManager = $this->parsedBodyManager->withParsedBody($data);
        return $new;
    }

    /**
     * @return array<string, mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributesManager->attributes;
    }

    public function getAttribute(string $name, mixed $default = null): mixed
    {
        return $this->attributesManager->getAttribute($name, $default);
    }

    public function withAttribute(string $name, mixed $value): static
    {
        $new = clone $this;
        $new->attributesManager = $this->attributesManager->withAttribute($name, $value);
        return $new;
    }

    public function withoutAttribute(string $name): static
    {
        $new = clone $this;
        $new->attributesManager = $this->attributesManager->withoutAttribute($name);
        return $new;
    }
}
