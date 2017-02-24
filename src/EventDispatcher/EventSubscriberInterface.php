<?php

namespace BornFree\TacticianDomainEvent\EventDispatcher;

interface EventSubscriberInterface
{
    /**
     * @return array
     */
    public function getSubscribedEvents();
}
