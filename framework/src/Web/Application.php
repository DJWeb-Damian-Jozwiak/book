<?php

declare(strict_types=1);

namespace DJWeb\Framework\Web;

use DJWeb\Framework\Base\Application as BaseApplication;
use DJWeb\Framework\Config\Config;
use DJWeb\Framework\Http\Kernel;
use DJWeb\Framework\Http\MiddlewareConfig;
use DJWeb\Framework\Routing\ControllerRegistrar;
use DJWeb\Framework\ServiceProviders\HttpServiceProvider;
use DJWeb\Framework\ServiceProviders\RouterServiceProvider;
use DJWeb\Framework\Storage\Session\Handlers\FileSessionHandler;
use DJWeb\Framework\Storage\Session\SessionConfiguration;
use DJWeb\Framework\Storage\Session\SessionManager;
use DJWeb\Framework\Storage\Session\SessionSecurity;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Application extends BaseApplication
{
    private Kernel $kernel;

    private ?SessionManager $sessionManager = null;
    public SessionManager $session {
        get {
           $this->sessionManager ??= SessionManager::create();
           return $this->sessionManager;
        }
    }

    protected function __construct()
    {
        parent::__construct();
        $this->registerServiceProvider(new HttpServiceProvider());
        $this->registerServiceProvider(new RouterServiceProvider());
        $this->kernel = new Kernel($this, new MiddlewareConfig());
    }

    public function handle(): ResponseInterface
    {
        $request = $this->get(ServerRequestInterface::class);
        return $this->kernel->handle($request);
    }

    public function loadRoutes(
        string $controllerNamespace,
        string $controllerDirectory
    ): void
    {
        new ControllerRegistrar($this)->registerControllers(
            $controllerNamespace,
            $controllerDirectory
        );
    }

    public function withRoutes(callable $callback): void
    {
        $this->kernel->withRoutes($callback);
    }
}
