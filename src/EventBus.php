<?php

namespace BornFree\TacticianDomainEvent;

interface EventBus
{
    /**
     * Dispatches an event
     *
     * @param mixed $event
     */
    public function dispatch($event);
}