<?php

declare(strict_types=1);

namespace DJWeb\Framework\Events;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

final readonly class EventDispatcher implements EventDispatcherInterface
{
    public function __construct(
        private ListenerProviderInterface $listenerProvider
    ) {
    }

    public function dispatch(object $event): object
    {
        $listeners = (array) $this->listenerProvider->getListenersForEvent($event);
        $listeners = array_filter($listeners, 'is_callable');

        foreach ($listeners as $listener) {
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                break;
            }

            $listener($event);
        }

        return $event;
    }
}
