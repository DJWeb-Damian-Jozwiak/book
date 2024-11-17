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
use Tests\Helpers\Models\Post;

#[RouteGroup('test-with-model')]
class TestControllerWithModel extends Controller
{
    #[Route('post/<post:\d+>', 'GET')]
    #[RouteParam('post', '\d+', bind: Post::class)]
    public function getPost(ServerRequestInterface $request, Post $post): ResponseInterface
    {
        $data = [
            'id' => $post->id,
            'status' => $post->status->value,
        ];
        return new Response()->withContent(json_encode($data));
    }

    #[Route('post/<post:\d+>', 'GET')]
    #[RouteParam('post', '\d+', bind: Post::class)]
    public function getInvalidPost(ServerRequestInterface $request, Post $post): ResponseInterface
    {
        $data = [
            'id' => $post->id,
            'status' => $post->status->value,
        ];
        return new Response()->withContent(json_encode($data));
    }
}