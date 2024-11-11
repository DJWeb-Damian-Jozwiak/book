<?php

declare(strict_types=1);

namespace Tests\Helpers;

use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Routing\Attributes\Route;
use DJWeb\Framework\Routing\Attributes\RouteGroup;
use DJWeb\Framework\Routing\Attributes\RouteParam;
use DJWeb\Framework\Routing\Controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[RouteGroup('test-with-attributes')]
class TestControllerWithAttributes extends Controller
{
    #[Route('/test1', 'GET')]
    public function test1(ServerRequestInterface $request): ResponseInterface
    {
        return new Response()->withContent('test1');
    }

    #[Route('/test2/<param:[a-z]+>', 'GET')]
    #[RouteParam('param', '[a-z]+')]
    public function test2(ServerRequestInterface $request, string $param): ResponseInterface
    {
        return new Response()->withContent($param);
    }
}