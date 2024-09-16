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
        $result = self::getBuildSchemePart($result, $uri);
        $result = self::getBuildAuthorityPart($result, $uri);
        $path = UriPathBuilder::buildUriPath($uri);
        $result .= $path;
        $result = self::getBuildQueryPart($result, $uri);
        return self::getBuildFragmentPart($result, $uri);
    }

    public static function getBuildSchemePart(string $result, UriInterface $uri): string
    {
        return UriPartBuilder::buildPart(
            $result,
            $uri->getScheme(...),
            suffix: ':'
        );
    }

    public static function getBuildAuthorityPart(string $result, UriInterface $uri): string
    {
        return UriPartBuilder::buildPart(
            $result,
            $uri->getAuthority(...),
            prefix: '//'
        );
    }

    public static function getBuildQueryPart(string $result, UriInterface $uri): string
    {
        return UriPartBuilder::buildPart(
            $result,
            $uri->getQuery(...),
            prefix: '?'
        );
    }

    public static function getBuildFragmentPart(string $result, UriInterface $uri): string
    {
        return UriPartBuilder::buildPart(
            $result,
            $uri->getFragment(...),
            prefix: '#'
        );
    }
}
