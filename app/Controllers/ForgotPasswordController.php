<?php

declare(strict_types=1);

namespace App\Controllers;

use App\FormValidators\ForgotPasswordDTO;
use Carbon\Carbon;
use DJWeb\Framework\DBAL\Models\Entities\User;
use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Routing\Attributes\Route;
use DJWeb\Framework\Routing\Attributes\RouteGroup;
use DJWeb\Framework\Routing\Controller;
use DJWeb\Framework\View\Inertia\Inertia;
use Psr\Http\Message\ResponseInterface;

#[RouteGroup('auth')]
class ForgotPasswordController extends Controller
{
    #[Route('/forgot-password', methods: 'GET')]
    public function showLinkRequestForm(): ResponseInterface
    {
        return Inertia::render('Auth/ForgotPassword.vue', [
            'title' => 'Reset Password'
        ]);
    }

    #[Route('/forgot-password', methods: 'POST')]
    public function sendResetLinkEmail(ForgotPasswordDTO $request): ResponseInterface
    {
        $user = User::query()->where('email', '=', $request->email)->first();

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $user->fill([
                'password_reset_token' => $token,
                'password_reset_expires' => Carbon::now()->addMinutes(60)
            ])->save();

        }

        return new Response()->withJson([
            'message' => 'Password reset link has been sent if the email exists'
        ]);
    }
}