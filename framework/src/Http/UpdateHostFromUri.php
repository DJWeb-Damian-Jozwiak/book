<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http;

use Psr\Http\Message\UriInterface;

final class UpdateHostFromUri
{
    public static function update(
        BaseRequest $request,
        UriInterface $uri
    ): BaseRequest|Request {
        $host = $uri->getHost();
        if ($uri->getPort() !== null) {
            $host .= ':' . $uri->getPort();
        }

        return $request->withHeader('Host', $host);
    }
}
