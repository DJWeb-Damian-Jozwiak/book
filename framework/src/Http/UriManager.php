<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http;

use DJWeb\Framework\Http\Helpers\AuthorityBuilder;
use DJWeb\Framework\Http\Helpers\QueryStringEncoder;
use Psr\Http\Message\UriInterface;

class UriManager implements UriInterface
{
    private string $scheme = '';
    private string $userInfo = '';
    private string $host = '';
    private ?int $port = null;
    private string $path = '';
    private string $query = '';
    private string $fragment = '';

    public function __construct(string $uri = '')
    {
        $parts = parse_url($uri);
        $this->scheme = $parts['scheme'] ?? '';
        $this->userInfo = $this->modifyUserInfo(
            $parts['user'] ?? '',
            $parts['pass'] ?? ''
        );
        $this->host = $parts['host'] ?? '';
        $this->port = isset($parts['port']) ? (int) $parts['port'] : null;
        $this->path = $parts['path'] ?? '';
        $this->query = $parts['query'] ?? '';
        $this->fragment = $parts['fragment'] ?? '';
    }

    public function __toString(): string
    {
        return UriStringBuilder::build($this);
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getAuthority(): string
    {
        return AuthorityBuilder::buildAuthority($this);
    }

    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }

    public function withScheme(string $scheme): static
    {
        $new = clone $this;
        $new->scheme = strtolower($scheme);
        return $new;
    }

    public function withUserInfo(string $user, ?string $password = null): static
    {
        $new = clone $this;
        $new->userInfo = $this->modifyUserInfo($user, $password);
        return $new;
    }

    public function withHost(string $host): static
    {
        $new = clone $this;
        $new->host = strtolower($host);
        return $new;
    }

    public function withPort(?int $port): static
    {
        $new = clone $this;
        $new->port = $port;
        return $new;
    }

    public function withPath(string $path): static
    {
        $new = clone $this;
        $path = trim($path, '/');
        $exploded = explode('/', $path);
        foreach ($exploded as $key => $part) {
            $exploded[$key] = rawurlencode($part);
        }
        $new->path = '/' . implode('/', $exploded);
        return $new;
    }

    public function withQuery(string $query): static
    {
        $new = clone $this;
        $new->query = $this->encodeQueryString($query);
        return $new;
    }

    public function withFragment(string $fragment): static
    {
        $new = clone $this;
        $new->fragment = rawurlencode($fragment);
        return $new;
    }

    private function modifyUserInfo(
        string $user,
        ?string $password = null
    ): string {
        $info = urlencode($user);
        if ($password) {
            $info .= ':' . urlencode($password);
        }
        return $info;
    }

    private function encodeQueryString(string $query): string
    {
        return QueryStringEncoder::encodeQueryString($query);
    }
}
