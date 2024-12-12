<?php

namespace Tests\Http\Middleware;

use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\Encryption\KeyGenerator;
use DJWeb\Framework\Http\Middleware\ValidationErrorMiddleware;
use DJWeb\Framework\Storage\Session\Handlers\FileSessionHandler;
use DJWeb\Framework\Storage\Session\SessionConfiguration;
use DJWeb\Framework\Storage\Session\SessionManager;
use DJWeb\Framework\Validation\ValidationError;
use DJWeb\Framework\Web\Application;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tests\BaseTestCase;

class ValidationErrorMiddlewareTest extends BaseTestCase
{
    private SessionManager $manager;
    private SessionConfiguration $configuration;
    private MockObject $config;
    private Application $app;
    private string $tempDir;
    public function testProcessPassesRequestWhenNoError(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willReturn($response);

        $middleware = new ValidationErrorMiddleware();

        $result = $middleware->process($request, $handler);

        $this->assertSame($response, $result);
    }

    public function testHandlesJsonRequestWithValidationError(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getHeaderLine')
            ->willReturnMap([
                ['Content-Type', 'application/json'],
                ['Accept', '*/*']
            ]);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $error = new ValidationError('email', 'Email is required');
        $validationError = new \DJWeb\Framework\Exceptions\Validation\ValidationError([$error]);
        $handler->method('handle')->willThrowException($validationError);

        $middleware = new ValidationErrorMiddleware();
        $response = $middleware->process($request, $handler);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(422, $response->getStatusCode());

        $responseData = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('Validation Error', $responseData['message']);
        $this->assertEquals(['field' => 'email', 'message' => 'Email is required'], $responseData['errors'][0]);
    }

    public function testHandlesWebRequestWithValidationError(): void
    {
        // Arrange
        $uri = $this->createMock(UriInterface::class);
        $uri->method('getPath')->willReturn('/form');

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getHeaderLine')
            ->willReturnMap([
                ['Content-Type', 'text/html'],
                ['Accept', 'text/html']
            ]);
        $request->method('getUri')->willReturn($uri);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $error = new ValidationError('email', 'Email is required');
        $validationError = new \DJWeb\Framework\Exceptions\Validation\ValidationError([$error]);
        $handler->method('handle')->willThrowException($validationError);

        $middleware = new ValidationErrorMiddleware();
        $response = $middleware->process($request, $handler);

        // Assert
        $this->assertInstanceOf(ResponseInterface::class, $response);

        $responseData = Application::getInstance()->session->get('errors');
        $responseData = json_decode($responseData, true);
        $this->assertEquals('Validation Error', $responseData['message']);
        $this->assertEquals(['field' => 'email', 'message' => 'Email is required'], $responseData['errors'][0]);

        $this->assertEquals('/form', $response->getHeaderLine('Location'));
        $this->assertEquals(302, $response->getStatusCode());
    }

    protected function setUp(): void
    {
        parent::setUp();
        Application::withInstance(null);
        $this->app = Application::getInstance();
        $this->config = $this->createMock(ConfigContract::class);
        $this->configuration = new SessionConfiguration();
        $this->app->bind('base_path', dirname(__DIR__));
        $this->tempDir = sys_get_temp_dir() . '/sessions_' . uniqid();
        $this->config
            ->expects($this->any())
            ->method('get')
            ->willReturnCallback(fn(string $key) => match ($key) {
                'session.cookie_params' => [
                    'lifetime' => 7200,
                    'path' => null,
                    'domain' => null,
                    'secure' =>  isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                    'httponly' => true,
                    'samesite' => 'Lax'
                ],
                'session.handler' => FileSessionHandler::class,
                'session.path' => $this->tempDir,
                'app.key' => new KeyGenerator()->generateKey(),
                default => null,
            });
        $this->app->set(ConfigContract::class, $this->config);

        mkdir($this->tempDir);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        array_map('unlink', glob($this->tempDir . '/*'));
        rmdir($this->tempDir);
    }
}