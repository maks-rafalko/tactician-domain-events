<?php

namespace BornFree\TacticianDomainEvent\EventDispatcher;

interface ContainsListenersInterface
{
    /**
     * @param $eventName
     * @param callable $listener
     * @return mixed
     */
    public function addListener($eventName, callable $listener);

    /**
     * @param string $eventName
     * @return callable[]
     */
    public function getListeners($eventName);

    /**
     * @param string $eventName
     * @return bool
     */
    public function hasListeners($eventName);
}