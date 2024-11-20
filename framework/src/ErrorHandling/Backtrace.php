<?php

declare(strict_types=1);

namespace DJWeb\Framework\ErrorHandling;

use Throwable;

final class Backtrace
{
    public function generate(Throwable $exception): TraceCollection
    {
        return TraceCollection::fromThrowable($exception);
    }
}
