<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Middleware;

use DJWeb\Framework\Exceptions\Validation\ValidationError;
use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Web\Application;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ValidationErrorMiddleware implements MiddlewareInterface
{
    private const JSON_CONTENT_TYPE = 'application/json';
    public function __construct(private ?Application $app = null) {
        $this->app ??= Application::getInstance();
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (ValidationError $error) {
            return $this->isJsonRequest($request) ?
                $this->handleJsonResponse($error) : $this->handleWebResponse($request, $error);
        }
    }


    private function isJsonRequest(ServerRequestInterface $request): bool
    {
        $contentType = $request->getHeaderLine('Content-Type');
        $acceptHeader = $request->getHeaderLine('Accept');

        return str_contains($contentType, self::JSON_CONTENT_TYPE)
            || str_contains($acceptHeader, self::JSON_CONTENT_TYPE);
    }

    private function formatErrorResponse(ValidationError $error): array
    {
        return [
            'message' => $error->getMessage(),
            'errors' => $error->validationErrors
        ];
    }

    private function handleJsonResponse(ValidationError $error): ResponseInterface
    {
        return new Response()->withJson($this->formatErrorResponse($error), status: 422);
    }

    private function handleWebResponse(ServerRequestInterface $request, ValidationError $error): ResponseInterface
    {
        $this->app->session->set(
            'errors',
            json_encode($this->formatErrorResponse($error))
        );
        return new Response()->redirect($request->getUri()->getPath());
    }

}