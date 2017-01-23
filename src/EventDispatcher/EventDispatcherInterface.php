<?php

namespace BornFree\TacticianDomainEvent\EventDispatcher;

interface EventDispatcherInterface
{
    /**
     * Dispatches an event
     *
     * @param mixed $event
     */
    public function dispatch($event);
}