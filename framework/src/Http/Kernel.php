<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Kernel
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new Response()->withContent('Hello world from kernel');
    }
}
