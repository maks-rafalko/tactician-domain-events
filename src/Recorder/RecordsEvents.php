<?php

namespace BornFree\TacticianDomainEvent\Recorder;

interface RecordsEvents extends ContainsRecordedEvents
{
    /**
     * Record an event
     *
     * @param mixed $event
     */
    public function record($event);
}