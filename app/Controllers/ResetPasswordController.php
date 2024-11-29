<?php

declare(strict_types=1);

namespace App\Controllers;

use App\FormValidators\ResetPasswordDTO;
use Carbon\Carbon;
use DJWeb\Framework\Auth\Auth;
use DJWeb\Framework\DBAL\Models\Entities\User;
use DJWeb\Framework\Http\Middleware\GuestMiddleware;
use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Routing\Attributes\Middleware;
use DJWeb\Framework\Routing\Attributes\Route;
use DJWeb\Framework\Routing\Attributes\RouteGroup;
use DJWeb\Framework\Routing\Controller;
use DJWeb\Framework\View\Inertia\Inertia;
use Psr\Http\Message\ResponseInterface;

#[RouteGroup('auth')]
class ResetPasswordController extends Controller
{
    #[Route('/reset-password/{token}', methods: 'GET')]
    public function showResetForm(string $token): ResponseInterface
    {
        return Inertia::render('Auth/ResetPassword.vue', [
            'title' => 'Reset Password',
            'token' => $token
        ]);
    }

    #[Route('/reset-password', methods: 'POST')]
    #[Middleware(beforeGlobal: [GuestMiddleware::class])]
    public function reset(ResetPasswordDTO $request): ResponseInterface
    {
        $data = $request->toArray();

        $user = User::query()->select()->where('password_reset_token', '=', $data['token'])
            ->where('password_reset_expires', '>', Carbon::now())
            ->first();

        $user->fill([
            'password' => $data['password'],
            'password_reset_token' => null,
            'password_reset_expires' => null
        ])->save();

        Auth::login($user);

        return new Response()->redirect('/');
    }
}