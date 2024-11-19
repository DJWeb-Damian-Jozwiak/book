<?php

namespace App\Controllers;

use App\Database\FormRequests\CategoryRequest;
use App\Database\Models\Category as CategoryModel;
use App\Dto\Category;
use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Routing\Attributes\Route;
use DJWeb\Framework\Routing\Attributes\RouteGroup;
use DJWeb\Framework\Routing\Attributes\RouteParam;
use DJWeb\Framework\Routing\Controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[RouteGroup('categories')]
class CategoriesController extends Controller
{
    #[Route('/', methods: ['GET'])]
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $categories = CategoryModel::query()->select()->limit(10)->get();
        $categories = array_map(Category::fromCategory(...), $categories);
        return new Response()->withContent(json_encode($categories, flags: JSON_PRETTY_PRINT));
    }

    #[Route('/<category:\d+>', methods: ['GET'])]
    #[RouteParam('category' , bind: CategoryModel::class)]
    public function show(ServerRequestInterface $request, CategoryModel $category): ResponseInterface
    {
        $dto = Category::fromCategory($category);
        return new Response()->withContent(json_encode($dto, flags: JSON_PRETTY_PRINT));
    }

    #[Route('/', methods: ['POST'])]
    public function store(CategoryRequest $request): ResponseInterface
    {
        return new Response()->withContent(json_encode($request->toArray()));
    }
}