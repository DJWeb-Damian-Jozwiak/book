<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Uri;

use DJWeb\Framework\Http\UriManager;
use Psr\Http\Message\UriInterface;

class UriBuilder
{
    public function createUriFromGlobals(): UriInterface
    {
        $uri = sprintf(
            '%s://%s%s%s',
            Scheme::get(),
            new Authority()->get(),
            Path::get(),
            Query::get()
        );
        return new UriManager($uri);
    }
}
