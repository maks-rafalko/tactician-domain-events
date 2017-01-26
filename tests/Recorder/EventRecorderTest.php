<?php

namespace BornFree\TacticianDomainEvent\Tests\Recorder;

use BornFree\TacticianDomainEvent\Recorder\EventRecorder;
use BornFree\TacticianDomainEvent\Recorder\RecordsEvents;
use BornFree\TacticianDomainEvent\Tests\Fixtures\UserWasCreated;

class EventRecorderTest  extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RecordsEvents
     */
    private $recorder;

    public function setUp()
    {
        $this->recorder = new EventRecorder();
    }

    /**
     * @test
     */
    public function it_records_events()
    {
        $this->recorder->record(new UserWasCreated());

        $this->assertCount(1, $this->recorder->releaseEvents());
    }

    /**
     * @test
     */
    public function it_releases_events_and_erases_them()
    {
        $this->recorder->record(new UserWasCreated());

        $this->assertCount(1, $this->recorder->releaseEvents());
        $this->assertCount(0, $this->recorder->releaseEvents());
    }

    /**
     * @test
     */
    public function it_erases_events()
    {
        $this->recorder->record(new UserWasCreated());

        $this->recorder->eraseEvents();

        $this->assertCount(0, $this->recorder->releaseEvents());
    }
}