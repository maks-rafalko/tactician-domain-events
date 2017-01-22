<?php

namespace BornFree\TacticianDomainEvent\Recorder;

interface ContainsRecordedEvents
{
    /**
     * Release and erase recorded events
     *
     * @return array
     */
    public function releaseEvents();

    /**
     * Erase recorded events
     *
     * @return self
     */
    public function eraseEvents();
}