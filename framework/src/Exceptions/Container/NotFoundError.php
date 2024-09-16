<?php

declare(strict_types=1);

namespace DJWeb\Framework\Exceptions\Container;

use DJWeb\Framework\Exceptions\NotFoundError as BaseNotFoundError;
use Psr\Container\NotFoundExceptionInterface;

class NotFoundError extends BaseNotFoundError implements NotFoundExceptionInterface
{
}
