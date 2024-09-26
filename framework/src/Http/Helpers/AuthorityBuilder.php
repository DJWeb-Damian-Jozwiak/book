<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Helpers;

use Psr\Http\Message\UriInterface;

final class AuthorityBuilder
{
    public static function buildAuthority(UriInterface $uri): string
    {
        $authority = $uri->getHost();
        $authority = self::buildUserInfo($uri, $authority);
        return self::buildPort($uri, $authority);
    }

    public static function buildUserInfo(
        UriInterface $uri,
        string $authority
    ): string {
        if ($uri->getUserInfo() !== '') {
            $authority = $uri->getUserInfo() . '@' . $authority;
        }
        return $authority;
    }

    public static function buildPort(
        UriInterface $uri,
        string $authority
    ): string {
        if ($uri->getPort() !== null) {
            $authority .= ':' . $uri->getPort();
        }
        return $authority;
    }
}
