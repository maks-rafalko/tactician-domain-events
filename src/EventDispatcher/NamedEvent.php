<?php

namespace BornFree\TacticianDomainEvent\EventDispatcher;

interface NamedEvent
{
    /**
     * @return string
     */
    public function getName();
}