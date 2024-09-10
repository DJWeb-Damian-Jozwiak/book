<?php

namespace DJWeb\Framework\ServiceProviders;

use DJWeb\Framework\Container\Contracts\ContainerInterface;
use DJWeb\Framework\Container\ServiceProvider;
use DJWeb\Framework\Http\Request;
use Psr\Http\Message\ServerRequestInterface;

class HttpServiceProvider extends ServiceProvider
{
    public function register(ContainerInterface $container): void
    {
        $container->set(
            key: ServerRequestInterface::class,
            value: Request::createFromSuperglobals(),
        );
    }
}