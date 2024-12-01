<?php

declare(strict_types=1);

namespace DJWeb\Framework\Events;

use DJWeb\Framework\Config\Config;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

class EventManager
{
    public EventDispatcherInterface $dispatcher{
        get => $this->_eventDispatcher ??= new EventDispatcher($this->listenerProvider);
    }
    public ListenerProviderInterface $listenerProvider{
        get => $this->_listenerProvider ??= $this->createListenerProvider();
    }
    private ?EventDispatcherInterface $_eventDispatcher = null;
    private ?ListenerProviderInterface $_listenerProvider = null;

    public function dispatch(object $event): object
    {
        return $this->dispatcher->dispatch($event);
    }

    private function createListenerProvider(): ListenerProviderInterface
    {
        $provider = new ListenerProvider();

        $eventConfig = Config::get('events.listeners');

        foreach ($eventConfig as $eventClass => $listeners) {
            foreach ((array) $listeners as $listenerClass) {
                $provider->addListener($eventClass, $listenerClass);
            }
        }

        return $provider;
    }
}
