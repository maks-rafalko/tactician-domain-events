<?php

namespace BornFree\TacticianDomainEvent\Tests\Fixtures;

use BornFree\TacticianDomainEvent\EventDispatcher\EventSubscriberInterface;

class UserEventSubscriber implements EventSubscriberInterface
{
    public function getSubscribedEvents()
    {
        return [
            UserWasCreated::class => [$this, '__invoke'],
        ];
    }

    public function __invoke()
    {
    }
}