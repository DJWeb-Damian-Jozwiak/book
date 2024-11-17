<?php

namespace Tests\Helpers;

use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Routing\Attributes\Route;
use DJWeb\Framework\Routing\Attributes\RouteGroup;
use DJWeb\Framework\Routing\Controller;
use Psr\Http\Message\ResponseInterface;

#[RouteGroup('validation')]
class TestControllerWithValidation extends Controller
{
    #[Route('index', 'POST')]
    public function index(SampleValidationDto $request): ResponseInterface
    {
        return new Response()->withContent(json_encode($request->toArray()));
    }
}