<?php

declare(strict_types=1);

namespace DJWeb\Framework\Events;

use Psr\EventDispatcher\StoppableEventInterface;

class BaseEvent implements StoppableEventInterface
{
    private bool $propagationStopped = false;

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }
}
