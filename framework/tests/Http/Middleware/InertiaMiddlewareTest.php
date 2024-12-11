<?php

namespace Tests\Http\Middleware;

use DJWeb\Framework\Console\Application;
use DJWeb\Framework\Http\JsonResponse;
use DJWeb\Framework\Http\Middleware\InertiaMiddleware;
use DJWeb\Framework\Http\MiddlewareStack;
use DJWeb\Framework\Http\Request\Psr17\RequestFactory;
use DJWeb\Framework\Http\Request\Psr7\ServerRequest;
use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Http\UriManager;
use DJWeb\Framework\Routing\Route;
use DJWeb\Framework\Routing\RouteHandler;
use DJWeb\Framework\Routing\Router;
use DJWeb\Framework\View\Inertia\Inertia;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Message\ServerRequestInterface;
use Tests\BaseTestCase;

class InertiaMiddlewareTest extends BaseTestCase
{
    private InertiaMiddleware $middleware;
    private MiddlewareStack $handler;
    private Router $router;
    private Application $app;

    public function testNormalRequestPassesThrough()
    {
        $this->router->addRoute(
            new Route('/test', 'GET',
                new RouteHandler(callback: fn() => new Response()->withStatus(200))
            )
        );

        $request = $this->createServerRequest('/test');

        $response = $this->handler->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testInertiaGetRequestWithRedirectReturns409()
    {
        $this->router->addRoute(
            new Route('/test-redirect', 'GET',
                new RouteHandler(callback: fn() =>
                new Response()
                    ->withStatus(302)
                    ->withHeader('Location', '/redirect-url')
                )
            )
        );

        $request = $this->createServerRequest('/test-redirect')
            ->withHeader('X-Inertia', 'true');

        $response = $this->handler->handle($request);

        $this->assertEquals(409, $response->getStatusCode());
        $this->assertEquals('/redirect-url', $response->getHeaderLine('X-Inertia-Location'));
    }

    public function testInertiaJsonResponseAddsVaryHeader()
    {
        $this->router->addRoute(
            new Route('/test-json', 'GET',
                new RouteHandler(callback: fn() =>
                new JsonResponse([
                    'component' => 'Test',
                    'props' => ['foo' => 'bar']
                ])
                )
            )
        );

        $request = $this->createServerRequest('/test-json')
            ->withHeader('X-Inertia', 'true');

        $response = $this->handler->handle($request);

        $this->assertTrue($response->hasHeader('Vary'));
        $this->assertEquals('X-Inertia', $response->getHeaderLine('Vary'));
    }

    public function testInvalidJsonResponseThrowsException()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid Inertia response: missing required fields');

        $this->router->addRoute(
            new Route('/test-invalid-json', 'GET',
                new RouteHandler(callback: fn() =>
                new JsonResponse(['invalid' => 'data'])
                )
            )
        );

        $request = $this->createServerRequest('/test-invalid-json')
            ->withHeader('X-Inertia', 'true');

        $this->handler->handle($request);
    }

    public function testInertiaShare()
    {
        $middlewareClass = new class extends InertiaMiddleware {
            protected function getShareData(): array
            {
                return ['test' => 'value'];
            }
        };
        $middleware = new $middlewareClass();
        $middleware->share();
        $this->assertEquals(['test' => 'value'], Inertia::getProps());

    }

    public static function redirectStatusCodeProvider(): array
    {
        return [
            [301], [302], [303], [307], [308]
        ];
    }

    #[DataProvider('redirectStatusCodeProvider')]
    public function testDifferentRedirectStatusCodesAreHandled(int $statusCode)
    {
        $this->router->addRoute(
            new Route('/test-redirect-status', 'GET',
                new RouteHandler(callback: fn() =>
                new Response()
                    ->withStatus($statusCode)
                    ->withHeader('Location', '/redirect-url')
                )
            )
        );

        $request = $this->createServerRequest('/test-redirect-status')
            ->withHeader('X-Inertia', 'true');

        $response = $this->handler->handle($request);

        $this->assertEquals(409, $response->getStatusCode());
        $this->assertEquals('/redirect-url', $response->getHeaderLine('X-Inertia-Location'));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app = Application::getInstance();
        $this->router = $this->app->get(Router::class);
        $this->middleware = new InertiaMiddleware();
        $this->handler = new MiddlewareStack($this->router);
        $this->handler->add($this->middleware);
    }

    private function createServerRequest(string $uri): ServerRequestInterface
    {
        $base = 'https://example.com/';
        return new RequestFactory()->createServerRequest('GET', $base.$uri);
    }
}