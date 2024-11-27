<?php

namespace App\Controllers;

use App\Database\Models\Category;
use App\Database\Models\Category as CategoryModel;
use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Routing\Attributes\Route;
use DJWeb\Framework\Routing\Attributes\RouteGroup;
use DJWeb\Framework\Routing\Attributes\RouteParam;
use DJWeb\Framework\Routing\Controller;
use DJWeb\Framework\View\Inertia\Inertia;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[RouteGroup('inertia')]
class InertiaController extends Controller
{
    #[Route('/categories', methods: 'GET')]
    public function home(): ResponseInterface
    {
        $categories = Category::query()->select()->limit(10)->get();
        $categories = array_map(\App\Dto\Category::fromCategory(...), $categories);
        return Inertia::render('Pages/Home/Index.vue', ['title' => 'inertia renderer', 'categories' => $categories]);
    }

    #[Route('/categories/<category:\d+>', methods: ['GET'])]
    #[RouteParam('category' , bind: CategoryModel::class)]
    public function show(ServerRequestInterface $request, CategoryModel $category): ResponseInterface
    {
        $dto = \App\Dto\Category::fromCategory($category);
        return Inertia::render('Pages/Home/Detail.vue', ['title' => 'inertia renderer', 'category' => $dto]);
    }
}