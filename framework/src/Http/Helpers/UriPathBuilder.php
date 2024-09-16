<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Helpers;

use Psr\Http\Message\UriInterface;

class UriPathBuilder
{
    public static function buildUriPath(UriInterface $uri): string
    {
        $path = $uri->getPath();
        if ($path !== '' && $path[0] !== '/' && $uri->getAuthority() !== '') {
            $path = '/' . $path;
        } elseif ($path === '/' && $uri->getAuthority() === '') {
            $path = '';
        }
        return $path;
    }
}
