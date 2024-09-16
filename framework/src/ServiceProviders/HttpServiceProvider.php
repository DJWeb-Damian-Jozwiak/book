<?php

namespace DJWeb\Framework\ServiceProviders;

use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Container\ServiceProvider;
use DJWeb\Framework\Http\RequestFactory;
use Psr\Http\Message\ServerRequestInterface;

class HttpServiceProvider extends ServiceProvider
{
    public function register(ContainerContract $container): void
    {
        $container->set(
            key: ServerRequestInterface::class,
            value: (new RequestFactory())->createRequest('GET', '/'),
        );
    }
}