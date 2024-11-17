<?php

namespace Tests\Helpers;

use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Routing\Attributes\Route;
use DJWeb\Framework\Routing\Controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class testControllerWithoutGroup extends Controller
{
    #[Route('controller-without-group')]
    public function test(ServerRequestInterface $request): ResponseInterface
    {
        return new Response();
    }
}