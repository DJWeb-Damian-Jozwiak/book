<?php

declare(strict_types=1);

namespace App\Controllers;

use DJWeb\Framework\Routing\Attributes\Route;
use DJWeb\Framework\Routing\Controller;
use DJWeb\Framework\View\Inertia\Inertia;

class ChatController extends Controller
{
    #[Route('/chat', methods: 'GET')]
    public function chat()
    {
        return Inertia::render('Pages/Chat.vue');
    }
}