<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Kernel
{
    public function handle(RequestInterface $request): ResponseInterface
    {
        return new Response()->withContent('Hello world from kernel');
    }
}
