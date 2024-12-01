<?php

declare(strict_types=1);

namespace App\Controllers;

use App\FormValidators\RegisterFormDTO;
use App\Mail\WelcomeMailable;
use DJWeb\Framework\Auth\Auth;
use DJWeb\Framework\Config\Config;
use DJWeb\Framework\DBAL\Models\Entities\Role;
use DJWeb\Framework\DBAL\Models\Entities\User;
use DJWeb\Framework\Events\Auth\UserRegisteredEvent;
use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Mail\MailerFactory;
use DJWeb\Framework\Routing\Attributes\Route;
use DJWeb\Framework\Routing\Attributes\RouteGroup;
use DJWeb\Framework\Routing\Controller;
use DJWeb\Framework\View\Inertia\Inertia;
use Psr\Http\Message\ResponseInterface;

#[RouteGroup('auth')]
class RegisterController extends Controller
{
    #[Route('/register', methods: 'GET')]
    public function register(): ResponseInterface
    {
        return Inertia::render('Auth/Register.vue', ['title' => 'Register']);
    }

    #[Route('/register', methods: 'POST')]
    public function store(RegisterFormDTO $request): ResponseInterface
    {
        $user = new User()->fill($request->toArray());
        $user->save();

        $defaultRole = Role::query()->select()->where('name' , '=', 'user')->fitst();

        if($defaultRole) {
            $user->addRole($defaultRole);
        }

        $this->events->dispatch(new UserRegisteredEvent($user));

        Auth::login($user);

        return new Response()
            ->withHeader('Location', '/')
            ->withStatus(303);
    }
}