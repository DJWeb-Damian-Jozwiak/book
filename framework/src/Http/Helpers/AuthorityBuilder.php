<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Helpers;

use Psr\Http\Message\UriInterface;

final class AuthorityBuilder
{
    public static function buildAuthority(UriInterface $uri): string
    {
        $authority = $uri->getHost();
        if ($uri->getUserInfo() !== '') {
            $authority = $uri->getUserInfo() . '@' . $authority;
        }
        if ($uri->getPort() !== null) {
            $authority .= ':' . $uri->getPort();
        }
        return $authority;
    }
}
