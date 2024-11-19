<?php

namespace App\Controllers;

use App\Database\Models\Category;
use DJWeb\Framework\Routing\Attributes\Route;
use DJWeb\Framework\Routing\Attributes\RouteGroup;
use DJWeb\Framework\Routing\Controller;
use DJWeb\Framework\View\Inertia\Inertia;
use Psr\Http\Message\ResponseInterface;

#[RouteGroup('inertia')]
class InertiaController extends Controller
{
    #[Route('/categories', methods: 'GET')]
    public function home(): ResponseInterface
    {
        $posts = Category::query()->select()->limit(10)->get();
        return Inertia::render('Pages/Home/Index.vue', ['title' => 'inertia renderer']);
    }
}