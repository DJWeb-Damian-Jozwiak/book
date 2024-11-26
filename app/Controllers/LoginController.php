<?php

declare(strict_types=1);

namespace App\Controllers;

use App\FormValidators\LoginFormDTO;
use Carbon\Carbon;
use DJWeb\Framework\Auth\Auth;
use DJWeb\Framework\DBAL\Models\Entities\User;
use DJWeb\Framework\Exceptions\Validation\ValidationError;
use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Routing\Attributes\Route;
use DJWeb\Framework\Routing\Attributes\RouteGroup;
use DJWeb\Framework\Routing\Controller;
use DJWeb\Framework\View\Inertia\Inertia;
use DJWeb\Framework\Web\Application;
use Psr\Http\Message\ResponseInterface;


#[RouteGroup('auth')]
class LoginController extends Controller
{
    #[Route('/login', methods: 'GET')]
    public function login(): ResponseInterface
    {
        return Inertia::render('Auth/Login.vue', [
            'title' => 'Login'
        ]);
    }

    #[Route('/login', methods: 'POST')]
    public function authenticate(LoginFormDTO $request): ResponseInterface
    {
        $data = $request->toArray();

        $loginType = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::query()->select()->where($loginType, '=', $request->login)->first();

        $attempt = Auth::attempt($user, $request->password, $request->remember);
        if(!$attempt) {
            $this->session->set('error', 'Invalid credentials');
            return new Response()->withHeader('Location', '/login')->withStatus(303);
        }
        $user->fill(['last_login_at' => Carbon::now()])->save();

        return new Response()
            ->withHeader('Location', '/')
            ->withStatus(303);

    }

    #[Route('/logout', methods: 'POST')]
    public function logout(): ResponseInterface
    {
        Auth::logout();
        return new Response()
            ->withHeader('Location', '/login')
            ->withStatus(303);
    }
}