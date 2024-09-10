<?php

namespace DJWeb\Framework;

use DJWeb\Framework\Container\Container;
use DJWeb\Framework\Container\Contracts\ServiceProviderInterface;
use DJWeb\Framework\Http\Kernel;
use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Routing\Router;
use DJWeb\Framework\ServiceProviders\HttpServiceProvider;
use DJWeb\Framework\ServiceProviders\RouterServiceProvider;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Application extends Container
{
    public function __construct()
    {
        parent::__construct();
        $this->registerServiceProvider(new HttpServiceProvider());
        $this->registerServiceProvider(new RouterServiceProvider());
    }


    public function handle(): ResponseInterface
    {
        $request = $this->get(ServerRequestInterface::class);
        return (new Kernel($this))
            ->withRoutes(function (Router $router) {
                $router->addRoute('GET', '/', function (RequestInterface $request) {
                    return (new Response())->setContent('Hello, World!');
                }, 'home');
            })
            ->handle($request);
    }

    protected function registerServiceProvider(ServiceProviderInterface $provider): void
    {
        $provider->register($this);
    }
}