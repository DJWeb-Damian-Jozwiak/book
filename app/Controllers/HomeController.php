<?php

declare(strict_types=1);

namespace App\Controllers;

use DJWeb\Framework\Routing\Attributes\Route;
use DJWeb\Framework\Routing\Controller;
use Psr\Http\Message\ResponseInterface;

class HomeController extends Controller
{
    #[Route('/twig', methods: 'GET')]
    public function twig(): ResponseInterface
    {
        return $this->render('twig/home.twig');
    }

    #[Route('/blade', methods: 'GET')]
    public function blade(): ResponseInterface
    {
        $this->withRenderer('blade');
        return $this->render('home.blade.php', ['title' => 'Blade']);
    }
}