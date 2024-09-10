<?php

namespace DJWeb\Framework\ServiceProviders;

use DJWeb\Framework\Container\Contracts\ContainerInterface;
use DJWeb\Framework\Container\ServiceProvider;
use DJWeb\Framework\Routing\RouteCollection;
use DJWeb\Framework\Routing\Router;

class RouterServiceProvider extends ServiceProvider
{
    public function register(ContainerInterface $container): void
    {
        $container->set(RouteCollection::class, new RouteCollection());

        $container->set(
            Router::class,
            new Router(
                $container,
                $container->get(RouteCollection::class)
            )
        );
    }
}