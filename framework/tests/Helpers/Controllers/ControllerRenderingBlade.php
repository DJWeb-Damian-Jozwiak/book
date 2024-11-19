<?php

declare(strict_types=1);

namespace Tests\Helpers\Controllers;

use DJWeb\Framework\Routing\Attributes\Route;
use DJWeb\Framework\Routing\Attributes\RouteGroup;
use DJWeb\Framework\Routing\Controller;
use Psr\Http\Message\ResponseInterface;

#[RouteGroup('blade')]
class ControllerRenderingBlade extends Controller
{
    #[Route('index')]
    public function index(): ResponseInterface
    {
        $this->withRenderer('blade');
        return $this->render('index.blade.php', ['user' => 'test', 'x' => 3]);
    }

    #[Route('missing')]
    public function missingTemplate(): ResponseInterface
    {
        $this->withRenderer('blade');
        return $this->render('index2.blade.php', ['user' => 'test', 'x' => 3]);
    }
}