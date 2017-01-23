<?php

namespace BornFree\TacticianDomainEvent\EventDispatcher;

interface EventListenerInterface
{
    /**
     * Handles an event
     *
     * @param mixed $event
     */
    public function handle($event);
}