<?php

namespace BornFree\TacticianDomainEvent\Recorder;

/**
 * Use this trait in classes which implement ContainsRecordedEvents to record and later release events.
 */
trait EventRecorderCapabilities
{
    /**
     * @var array
     */
    private $recordedEvents = [];

    /**
     * Release and clear recorded events
     *
     * @return array
     */
    public function releaseEvents()
    {
        $events = $this->recordedEvents;

        $this->eraseEvents();

        return $events;
    }

    /**
     * Erase all events
     */
    public function eraseEvents()
    {
        $this->recordedEvents = [];
    }

    /**
     * Record an event.
     *
     * @param mixed $event
     */
    public function record($event)
    {
        $this->recordedEvents[] = $event;
    }
}
