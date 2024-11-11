<?php

use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Routing\Route;
use DJWeb\Framework\Routing\RouteHandler;
use DJWeb\Framework\Routing\Router;
use Psr\Http\Message\RequestInterface;

return function (Router $router) {
    $router->addRoute(
        new Route(
            path: '/',
            method: "GET",
            handler: new RouteHandler(
                callback: fn(RequestInterface $request) => new Response()->withContent('Hello World from routes')
            )
        )
    );
};