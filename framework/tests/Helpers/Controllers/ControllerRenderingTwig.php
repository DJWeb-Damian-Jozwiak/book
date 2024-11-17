<?php

declare(strict_types=1);

namespace Tests\Helpers\Controllers;

use DJWeb\Framework\Routing\Attributes\Route;
use DJWeb\Framework\Routing\Attributes\RouteGroup;
use DJWeb\Framework\Routing\Controller;
use Psr\Http\Message\ResponseInterface;

#[RouteGroup('twig')]
class ControllerRenderingTwig extends Controller
{
    #[Route('index')]
    public function index(): ResponseInterface
    {
        $this->withRenderer('twig');
        return $this->render('index.twig', ['user' => 'test']);
    }

    #[Route('unknown-adapter')]
    public function unknownAdapter(): ResponseInterface
    {
        $this->withRenderer('unknown-adapter');
        return $this->render('index.twig', ['user' => 'test']);
    }
}