<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http;

use DJWeb\Framework\Http\Uri\FragmentTrait;
use DJWeb\Framework\Http\Uri\HostTrait;
use DJWeb\Framework\Http\Uri\PathTrait;
use DJWeb\Framework\Http\Uri\PortTrait;
use DJWeb\Framework\Http\Uri\QueryTrait;
use DJWeb\Framework\Http\Uri\SchemeTrait;
use DJWeb\Framework\Http\Uri\UserInfoTrait;
use Psr\Http\Message\UriInterface;
use ReflectionClass;

class Uri implements UriInterface
{
    use SchemeTrait;
    use UserInfoTrait;
    use HostTrait;
    use PortTrait;
    use PathTrait;
    use QueryTrait;
    use FragmentTrait;

    public function __construct(
        string $scheme = 'http',
        string $userInfo = '',
        string $host = '',
        ?int $port = 80,
        string $path = '',
        string $query = '',
        string $fragment = ''
    ) {
        $this->scheme = $scheme;
        $this->validateScheme($scheme);
        $this->userInfo = $userInfo;
        $this->host = $host;
        $this->port = $port;
        $this->validatePort($port);
        $this->path = $path;
        $this->query = $query;
        $this->fragment = $fragment;
    }
    public function __toString(): string
    {
        $uri = $this->scheme ? $this->scheme . ':' : '';
        $authority = $this->getAuthority();
        if ($authority) {
            $uri .= '//' . $authority;
        }
        $uri .= $this->path;
        if ($this->query) {
            $uri .= '?' . $this->query;
        }
        if ($this->fragment) {
            $uri .= '#' . $this->fragment;
        }
        return $uri;
    }

    private function clone(
        UriInterface $obj,
        string $propertyName,
        string|int|float|null $propertyValue
    ): static {
        $clone = clone $obj;
        $reflection = new ReflectionClass($clone);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($clone, $propertyValue);
        /** @phpstan-ignore-next-line */
        return $clone;
    }
}
