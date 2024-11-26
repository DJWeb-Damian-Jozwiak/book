<?php

declare(strict_types=1);

namespace DJWeb\Framework\Auth;

use Carbon\Carbon;
use DJWeb\Framework\DBAL\Models\Entities\User;
use DJWeb\Framework\Storage\CookieOptions;
use DJWeb\Framework\Web\Application;

class UserManager
{
    private const string SESSION_KEY = 'auth_user_id';
    private const string REMEMBER_TOKEN_COOKIE = 'remember_token';
    private const int REMEMBER_COOKIE_EXPIRY = 60; // 60 days

    private ?User $user = null;

    public function attempt(User $user, string $password, bool $remember = false): bool
    {
        if (! password_verify($password, $user->password)) {
            return false;
        }

        if (! $user->is_active) {
            return false;
        }

        $this->login($user, $remember);
        return true;
    }

    public function user(): ?User
    {
        if ($this->user !== null) {
            return $this->user;
        }

        $userId = Application::getInstance()->session->get(self::SESSION_KEY);

        if ($userId) {
            $this->user = User::query()->select()->where('id', '=', $userId)->first();
            return $this->user;
        }

        $rememberToken = Application::getInstance()->cookies->get(self::REMEMBER_TOKEN_COOKIE);
        if ($rememberToken) {
            $user = User::query()->select()->where('remember_token', '=', $rememberToken)->first();
            if ($user) {
                $this->login($user, true);
                return $user;
            }
            Application::getInstance()->cookies->remove(self::REMEMBER_TOKEN_COOKIE);
        }

        return null;
    }

    public function check(): bool
    {
        return $this->user() !== null;
    }

    public function guest(): bool
    {
        return ! $this->check();
    }

    public function login(User $user, bool $remember = false): void
    {
        $this->user = $user;
        Application::getInstance()->session->set(self::SESSION_KEY, $user->id);

        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $user->fill(['remember_token' => $token])->save();

            Application::getInstance()->cookies->set(
                self::REMEMBER_TOKEN_COOKIE,
                $token,
                new CookieOptions(expires: Carbon::now()->addDays(self::REMEMBER_COOKIE_EXPIRY)->getTimestamp())
            );
        }
    }

    public function logout(): void
    {
        $this->user?->fill(['remember_token' => null])->save();

        $this->user = null;
        Application::getInstance()->session->remove(self::SESSION_KEY);
        Application::getInstance()->cookies->remove(self::REMEMBER_TOKEN_COOKIE);
    }
}
