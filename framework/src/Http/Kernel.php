<?php

namespace DJWeb\Framework\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Kernel
{
    public function handle(RequestInterface $request): ResponseInterface
    {
        return (new Response(200))->setContent("Hello world from kernel");
    }
}