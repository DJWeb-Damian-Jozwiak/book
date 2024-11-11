<?php

declare(strict_types=1);

namespace DJWeb\Framework\Routing;

use DJWeb\Framework\Container\Container;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Web\Application;

abstract class Controller
{
    public function __construct(
        public readonly ContainerContract $container
    ) {
        $this->registerInContainer();
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
