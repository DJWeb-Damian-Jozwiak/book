<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Helpers;

use Psr\Http\Message\UriInterface;

class UriPathBuilder
{
    public static function buildUriPath(UriInterface $uri): string
    {
        $path = $uri->getPath();
        $authority = $uri->getAuthority();
        if (PathCheckerWithAuthority::check($path, $authority)) {
            $path = '/' . $path;
        } elseif (PathCheckerWithoutAuthority::check($path, $authority)) {
            $path = '';
        }
        return $path;
    }
}
