<?php

declare(strict_types=1);

namespace DJWeb\Framework\ServiceProviders;

use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Container\ServiceProvider;
use DJWeb\Framework\Http\Request\Psr17\RequestFactory;
use DJWeb\Framework\Log\LoggerFactory;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class HttpServiceProvider extends ServiceProvider
{
    public function register(ContainerContract $container): void
    {
        $container->set(
            key: ServerRequestInterface::class,
            value: new RequestFactory()->createFromGlobals(),
        );
    }
}
