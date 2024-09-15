<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http;

use DJWeb\Framework\Http\Helpers\UriPartBuilder;
use DJWeb\Framework\Http\Helpers\UriPathBuilder;
use Psr\Http\Message\UriInterface;

final readonly class UriStringBuilder
{
    public static function build(UriInterface $uri): string
    {
        $result = '';
        $result = UriPartBuilder::buildPart(
            $result,
            $uri->getScheme(...),
            suffix: ':'
        );
        $result = UriPartBuilder::buildPart(
            $result,
            $uri->getAuthority(...),
            prefix: '//'
        );
        $path = UriPathBuilder::buildUriPath($uri);
        $result .= $path;
        $result = UriPartBuilder::buildPart(
            $result,
            $uri->getQuery(...),
            prefix: '?'
        );
        return UriPartBuilder::buildPart(
            $result,
            $uri->getFragment(...),
            prefix: '#'
        );
    }
}
