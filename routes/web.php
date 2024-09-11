<?php

use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Routing\Router;
use Psr\Http\Message\RequestInterface;

return function (Router $router) {
    $router->addRoute('GET', '/', function (RequestInterface $request) {
        return (new Response())->setContent('Hello, World from routes!');
    }, 'home');
};