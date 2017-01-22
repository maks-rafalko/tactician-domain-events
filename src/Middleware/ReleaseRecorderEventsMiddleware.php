<?php

namespace BornFree\TacticianDomainEvent\Middleware;

use BornFree\TacticianDomainEvent\EventBus;
use BornFree\TacticianDomainEvent\Recorder\ContainsRecordedEvents;
use League\Tactician\Middleware;

class ReleaseRecorderEventsMiddleware implements Middleware
{
    /**
     * @var ContainsRecordedEvents
     */
    private $eventRecorder;

    /**
     * @var EventBus
     */
    private $eventBus;

    /**
     * ReleaseRecorderEventsMiddleware constructor.
     *
     * @param ContainsRecordedEvents $eventRecorder
     * @param EventBus $eventBus
     */
    public function __construct(ContainsRecordedEvents $eventRecorder, EventBus $eventBus)
    {
        $this->eventRecorder = $eventRecorder;
        $this->eventBus = $eventBus;
    }

    /**
     * Dispatches all the recorded events in the EventBus and erases them
     *
     * @param object $command
     * @param callable $next
     *
     * @return void
     *
     * @throws \Exception
     */
    public function execute($command, callable $next)
    {
        try {
            $next($command);
        } catch (\Exception $exception) {
            $this->eventRecorder->eraseEvents();

            throw $exception;
        }

        $recordedEvents = $this->eventRecorder->releaseEvents();

        foreach ($recordedEvents as $event) {
            $this->eventBus->dispatch($event);
        }
    }
}