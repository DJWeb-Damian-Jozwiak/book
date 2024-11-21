<?php

declare(strict_types=1);

namespace DJWeb\Framework\Storage\Session\Handlers;

use DJWeb\Framework\Config\Config;
use DJWeb\Framework\DBAL\Models\Entities\Session;
use DJWeb\Framework\Storage\Session\SessionSecurity;

class DatabaseSessionHandler implements \SessionHandlerInterface
{
    public function __construct(
        private readonly SessionSecurity $security
    ) {
    }

    public function close(): bool
    {
        return true;
    }

    public function destroy(string $id): bool
    {
       return Session::query()->delete()->where('id', '=', $id)->delete();
    }

    public function gc(int $max_lifetime): int|false
    {
        $expired = time() - $max_lifetime;
        Session::query()->delete()->where('last_activity', '<', $expired)->delete();
        return 1;
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function read(string $id): string|false
    {
        $session = Session::query()->select()->where('id', '=', $id)->first();
        if (! $session) {
            return '';
        }
        $lifetime = Config::get('session.cookie_params.lifetime', 0);
        if ($session->last_activity + $lifetime < time()) {
            $this->destroy($id);
            return '';
        }
        return $this->security->decrypt($session->payload);
    }

    public function write(string $id, string $data): bool
    {
        $encrypted = $this->security->encrypt($data);

        $session = Session::query()->select()->where('id', '=', $id)->first();
        $session ??= new Session();
        $session->id = $id;
        $session->payload = $encrypted;
        $session->last_activity = time();
        $session->user_ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $session->user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $session->user_id = null;

        $session->save();
        return true;
    }
}
