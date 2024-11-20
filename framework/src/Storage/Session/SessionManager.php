<?php

declare(strict_types=1);

namespace DJWeb\Framework\Storage\Session;

use DJWeb\Framework\Config\Config;
use DJWeb\Framework\Log\Log;
use DJWeb\Framework\Storage\Session\Handlers\FileSessionHandler;


final class SessionManager
{
    private bool $started = false;
    /**
     * @var array<string, mixed>
     */
    private array $options;

    private \SessionHandlerInterface $handler;

    public function __construct(private readonly SessionConfiguration $config)
    {
        $this->options = (array)Config::get('session.cookie_params');
        $this->handler = $this->config->getHandler(Config::get('session.handler'));
        session_set_cookie_params($this->options);
        session_set_save_handler($this->handler, true);
    }

    public static function create(): self
    {
        $path = Config::get('session.path');
        $configSession = new SessionConfiguration();
        $security = new SessionSecurity();
        $configSession->registerHandler(new FileSessionHandler($path, $security));
        $manager = new SessionManager($configSession);
        $manager->start();
        return $manager;
    }

    public function start(): bool
    {
        if ($this->started) {
            return true;
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            $this->started = true;
            return true;
        }

        Log::debug('Before session_start(). Cookie: ' . print_r($_COOKIE, true));

        session_name('PHPSESSID');
        if (session_start()) {
            $this->started = true;
            return true;
        }

        return false;
    }

    public function getId(): string
    {
        return session_id();
    }

    public function regenerateId(bool $deleteOldSession = false): bool
    {
        return session_regenerate_id($deleteOldSession);
    }

    public function destroy(): bool
    {
        if (!$this->started) {
            return false;
        }

        $this->started = false;
        return session_destroy();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function all(): array
    {
        return $_SESSION;
    }


    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function clear(): void
    {
        $_SESSION = [];
    }
}