<?php

namespace BornFree\TacticianDomainEvent\Tests\Fixtures;

use BornFree\TacticianDomainEvent\EventDispatcher\NamedEvent;

class NamedEventWasRaised implements NamedEvent
{
    public function getName()
    {
        return 'named.event';
    }
}