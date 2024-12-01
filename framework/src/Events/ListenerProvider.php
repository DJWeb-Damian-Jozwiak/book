<?php

declare(strict_types=1);

namespace DJWeb\Framework\Events;

use Psr\EventDispatcher\ListenerProviderInterface;

class ListenerProvider implements ListenerProviderInterface
{
    /**
     * @var array<string, array<callable>>
     */
    private array $listeners = [];

    public function addListener(string $eventClass, callable $listener): void
    {
        $this->listeners[$eventClass] ??= [];
        $this->listeners[$eventClass][] = $listener;
    }

    public function getListenersForEvent(object $event): iterable
    {
        $eventClass = $event::class;
        return $this->listeners[$eventClass] ?? [];
    }
}
