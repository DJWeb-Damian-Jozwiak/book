<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Middleware;

use Carbon\Carbon;
use DJWeb\Framework\Base\Application;
use DJWeb\Framework\Http\Middleware\RequestLogger\ContextBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class RequestLoggerMiddleware implements MiddlewareInterface
{
    private const REQUEST_START_TIME = 'request_start_time';
    private LoggerInterface $logger;
    public function __construct(
        private readonly ContextBuilder $contextBuilder
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->logger = Application::getInstance()->logger;
        $startTime = Carbon::now();
        $request = $request->withAttribute('request_start_time', $startTime);
        $request = $request->withAttribute(self::REQUEST_START_TIME, $startTime);

        try {
            $response = $handler->handle($request);
            $context = $this->contextBuilder->buildSuccessContext($request, $response, $startTime);

            $this->logger->info(
                $this->contextBuilder->buildSuccessMessage($request, $response, $startTime),
                $context
            );

            return $response;
        } catch (Throwable $e) {
            $context = $this->contextBuilder->buildExceptionContext($request, $startTime, $e);

            $this->logger->error(
                $this->contextBuilder->buildExceptionMessage($request, $startTime, $e),
                $context
            );

            throw $e;
        }
    }
}
