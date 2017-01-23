<?php

namespace BornFree\TacticianDomainEvent\Middleware;

use BornFree\TacticianDomainEvent\EventDispatcher\EventDispatcherInterface;
use BornFree\TacticianDomainEvent\Recorder\ContainsRecordedEvents;
use League\Tactician\Middleware;

class ReleaseRecordedEventsMiddleware implements Middleware
{
    /**
     * @var ContainsRecordedEvents
     */
    private $eventRecorder;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * ReleaseRecorderEventsMiddleware constructor.
     *
     * @param ContainsRecordedEvents $eventRecorder
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(ContainsRecordedEvents $eventRecorder, EventDispatcherInterface $eventDispatcher)
    {
        $this->eventRecorder = $eventRecorder;
        $this->eventDispatcher = $eventDispatcher;
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
            $this->eventDispatcher->dispatch($event);
        }
    }
}