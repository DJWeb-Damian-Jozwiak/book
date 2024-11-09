<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Middleware\RequestLogger;

use Carbon\Carbon;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ContextBuilder
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param Carbon $startTime
     *
     * @return array<string|int, mixed>
     */
    public function buildSuccessContext(
        ServerRequestInterface $request,
        ResponseInterface $response,
        Carbon $startTime
    ): array
    {
        return [
            ...$this->buildBaseContext($request, $startTime),
            'status_code' => $response->getStatusCode(),
            'completed_at' => Carbon::now()->toIso8601String(),
        ];
    }

    /**
     * @param ServerRequestInterface $request
     * @param Carbon $startTime
     * @param \Throwable $exception
     *
     * @return array<string|int, mixed>
     */
    public function buildExceptionContext(
        ServerRequestInterface $request,
        Carbon $startTime,
        \Throwable $exception
    ): array
    {
        return [
            ...$this->buildBaseContext($request, $startTime),
            'exception' => $exception::class,
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'failed_at' => Carbon::now()->toIso8601String(),
        ];
    }
    public function buildExceptionMessage(
        ServerRequestInterface $request,
        Carbon $startTime,
        \Throwable $exception
    ): string
    {
        $context = $this->buildExceptionContext($request, $startTime, $exception);
        return sprintf(
            '[%s] Request failed: %s %s - %s',
            $context['timestamp'],
            $context['method'],
            $context['uri'],
            $context['message']
        );
    }
    public function buildSuccessMessage(
        ServerRequestInterface $request,
        ResponseInterface $response,
        Carbon $startTime,
    ): string
    {
        $context = $this->buildSuccessContext($request, $response, $startTime);
        return sprintf(
            '[%s] Request completed: %s %s [%d] in %.2fms',
            $context['timestamp'],
            $context['method'],
            $context['uri'],
            $context['status_code'],
            $context['duration']
        );
    }

    /**
     * @param ServerRequestInterface $request
     * @param Carbon $startTime
     *
     * @return array<string|int, mixed>
     */
    private function buildBaseContext(
        ServerRequestInterface $request,
        Carbon $startTime
    )
    {
        return [
            'timestamp' => Carbon::now()->toIso8601String(),
            'method' => $request->getMethod(),
            'uri' => (string) $request->getUri(),
            'duration' => round($startTime->diffInMicroseconds(Carbon::now()) / 1000, 2),
            'ip' => $request->getServerParams()['REMOTE_ADDR'] ?? null,
            'user_agent' => $request->getHeaderLine('User-Agent'),
            'started_at' => $startTime->toIso8601String(),
        ];
    }
}
