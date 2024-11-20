<?php

declare(strict_types=1);

namespace DJWeb\Framework\Routing;

use DJWeb\Framework\Container\Container;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Storage\Session\SessionManager;
use DJWeb\Framework\View\Contracts\RendererContract;
use DJWeb\Framework\View\ViewManager;
use DJWeb\Framework\Web\Application;
use Psr\Http\Message\ResponseInterface;

abstract class Controller
{
    public SessionManager $session {
        get => Application::getInstance()->session;
    }
    protected string $viewRenderer = 'twig';
    protected ViewManager $viewManager;

    private ?RendererContract $renderer = null;

    public function __construct(
        public readonly ContainerContract $container
    ) {
        $this->registerInContainer();
        $this->viewManager = new ViewManager();
    }

    public function withRenderer(string $renderer): void
    {
        $this->viewRenderer = $renderer;
    }

    public function render(string $view, array $data = []): ResponseInterface
    {
        $this->renderer ??= $this->viewManager->build($this->viewRenderer);
        return $this->viewManager->withRenderer($this->renderer)->make($view, $data)->render();
    }

    private function registerInContainer(): void
    {
        /** @var Application $app */
        $app = $this->container->get(Container::class);
        $items = new RegisterControllerRoutes()->register($this);
        $routes = $items instanceof RouteGroup ? $items->routes : $items;
        $app->withRoutes(static function (Router $router) use ($routes): void {
            foreach ($routes as $route) {
                $router->addRoute($route);

            }
        });
    }
}
