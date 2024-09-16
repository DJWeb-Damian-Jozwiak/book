<?php

declare(strict_types=1);

namespace DJWeb\Framework\Exceptions\Container;

use DJWeb\Framework\Exceptions\BaseRuntimeError;
use Psr\Container\ContainerExceptionInterface;

class ContainerError extends BaseRuntimeError implements ContainerExceptionInterface
{
}
