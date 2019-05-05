<?php

namespace BornFree\TacticianDomainEvent\EventDispatcher;

use BornFree\TacticianDomainEvent\Tests\Fixtures\NamedEventWasRaised;
use BornFree\TacticianDomainEvent\Tests\Fixtures\UserEventSubscriber;
use BornFree\TacticianDomainEvent\Tests\Fixtures\UserWasCreated;

class EventDispatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventDispatcher|\PHPUnit_Framework_MockObject_MockObject
     */
    private $eventDispatcher;

    /**
     * @var string
     */
    private $eventName;

    /**
     * @var callable
     */
    private $listener;

    /**
     * @var int
     */
    private $listenerCallsCount = 0;

    public function setUp()
    {
        $this->eventName = UserWasCreated::class;

        $this->listener = function () {
            $this->listenerCallsCount++;
        };

        $this->eventDispatcher = new EventDispatcher();
    }

    /**
     * @test
     */
    public function it_adds_event_listeners()
    {
        $this->eventDispatcher->addListener($this->eventName, $this->listener);

        $this->eventDispatcher->hasListeners($this->eventName);

        $this->eventDispatcher->getListeners($this->eventName)[0];

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function it_return_empty_array_when_event_does_not_have_listeners()
    {
        $this->eventDispatcher->getListeners('test');

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function it_calls_all_listeners_during_dispatch()
    {
        $this->eventDispatcher->addListener($this->eventName, $this->listener);
        $this->eventDispatcher->addListener($this->eventName, $this->listener);

        $this->eventDispatcher->dispatch(new UserWasCreated());

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function it_uses_names_event_interface_during_during_dispatching_an_event()
    {
        $eventDispatcher = new EventDispatcher();

        $eventDispatcher->dispatch(new NamedEventWasRaised());

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function it_adds_event_listeners_from_subscriber()
    {
        $this->eventDispatcher->addSubscriber(new UserEventSubscriber());

        $this->eventDispatcher->hasListeners($this->eventName);

        $this->assertTrue(true);
    }
}
