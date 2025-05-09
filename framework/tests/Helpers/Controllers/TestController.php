<?php

namespace Tests\Helpers\Controllers;

use DJWeb\Framework\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TestController
{
    public function testMethod(ServerRequestInterface $request): ResponseInterface
    {
        $response = new Response();
        $response->withContent('ok');
        return $response;
    }
}