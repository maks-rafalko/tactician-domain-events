---
currentMenu: domain_events
---

There are two possible ways to record Domain Events:

* Using the public service, `EventRecorder`
* Using the Entity

### Public Recorder

```php
use BornFree\TacticianDomainEvent\Recorder\EventRecorder;

// it can be a service in your DI container
$eventRecorder = new EventRecorder();

$event = new TaskWasCreated('Finish review');

$eventRecorder->record($event);

// later in the source code we release the events and dispatch them
$recordedEvents = $eventRecorder->releaseEvents();
```

`$recordedEvents` array contains all recorded Domain Events, in this case the `TaskWasCreated` one, and can be dispatched with any dispatcher that implements `EventDispatcherInterface`:
 
 ```php
 namespace BornFree\TacticianDomainEvent\EventDispatcher;
 
 interface EventDispatcherInterface
 {
     public function dispatch($event);
 }
 ```
 
 This lib contains the very basic implementation of `EventDispatcher`, but you can implement your own, see [Event Dispatcher](#event-dispatcher) section below

### Record events in Entity

When you use domain events, your domain entities will generate events while you change or create them. You record those events inside the entity and when the changes have been persisted and the database transaction has succeeded, you should collect the recorded events and handle them:

```php
$task = new Task('Finish review'); // records `TaskWasCreated` domain event

// start transaction
$entityManager->persist($entity);
// commit transaction

$events = $entity->releaseEvents();

// dispatch the events
foreach ($events as $event) {
    $eventDispatcher->dispatch($event);
}
```

This is possible thanks to implementing `RecordsEvents` interface that has a `record()` method. You can use a built in `EventRecorderCapabilities` trait:

```php
use BornFree\TacticianDomainEvent\Recorder\ContainsRecordedEvents;
use BornFree\TacticianDomainEvent\Recorder\EventRecorderCapabilities;

class Task implements ContainsRecordedMessages
{
    use EventRecorderCapabilities;

    public function __construct($name)
    {
        $this->record(new TaskWasCreated($name));
    }
}
```

## Event Dispatcher

Using the built in `EventDispatcher`, we can handle all recorded events:

```php
use BornFree\TacticianDomainEvent\EventDispatcher;
...
$recordedEvents = $eventRecorder->releaseEvents();

$eventDispatcher = new EventDispatcher();

foreach ($recordedEvents as $event) {
    $eventDispatcher->dispatch($event);
}
```

Now it's time to add listeners and react to dispatched events.

### Add Event Listeners

The built in `EventDispatcher` accepts any `callable` as a listener. It means you can use regular methods, static methods, callable object with `__invoke()` function and even closures.

Let's write an event listener that sends notification when the task is created:

```php
class SendPushNotificationListener
{
    public function __construct(PushNotificationSender $sender) {...}
    
    public function __invoke($event)
    {
        $this->sender->send($event->getName());
    }
}

$listener = new SendPushNotificationListener($sender);

$eventDispatcher->addListener(TaskWasCreated::class, $listener);
```

Or using a closure:

```php
$eventDispatcher->addListener(
    TaskWasCreated::class,
    function ($event) use ($sender) {
        $sender->send($event->getName());
    }
);

```

### Add Event Subscribers

It's also possible to add event subscribers: classes that handles multiple events. Your subscriber have to implement the interface:

```php
interface EventSubscriberInterface
{
    /**
     * @return array
     */
    public function getSubscribedEvents();
}
```

Method `getSubscribedEvents` should return an array, where keys are events and values are callables:

```php
class MailerListener implements EventSubscriberInterface
{
    public function getSubscribedEvents()
    {
        return [
            UserWasCreated => [$this, 'sendActivationLink'],
            UserWasDeactivated => [$this, 'sendDeactivationInfo'],
        ];
    }

    public function sendActivationLink()
    {
        // ...
    }

    public function sendDeactivationInfo()
    {
        // ...
    }
}
```

And adding it to dispatcher:

```php
$eventDispatcher->addSubscriber(new MailerListener());
```

## Handling events with Tactician command bus

### Handling recorded events from public recorder

Events are recorded while a command is handled. We only want to handle the events themselves **after** the command has completely and successfully been handled. The best option to accomplish this is to add a middleware to the command bus. This middleware needs the event recorder to find out which events were recorded during the handling of commands, and it needs the event dispatcher to actually dispatch the recorded events:

```php
use League\Tactician\CommandBus;
use League\Tactician\Doctrine\ORM\TransactionMiddleware;
use BornFree\TacticianDomainEvent\Middleware\ReleaseRecordedEventsMiddleware;

// see the previous sections about $eventRecorder and $eventDispatcher
$releaseRecordedEventsMiddleware = new ReleaseRecordedEventsMiddleware($eventRecorder, $eventDispatcher);

$commandBus = new CommandBus(
    [
        $releaseRecordedEventsMiddleware, // it should be before transaction middleware
        $transactionMiddleware,
        $commandHandlerMiddleware
    ]
);

```

We only want events to be handled when we know that everything else has gone well and transaction has been committed.

### Handling domain events from Entities

When you privately record events inside your domain entities, you need to collect those recorded events manually. Your database abstraction library, ORM or ODM probably offers a way to hook into the process of persisting the entities and collecting them somehow. After the command has been handled successfully and the transaction has been committed, you can iterate over those entities and collect their recorded events.

> #### Handling domain events with Doctrine ORM
> 
> This library comes with a [Doctrine ORM bridge](https://bornfreee.github.io/tactician-doctrine-domain-events). Using this package you can collect recorded events from Doctrine ORM entities *automatically* using built in Doctrine Event Subscriber
