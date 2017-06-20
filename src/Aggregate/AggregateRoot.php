<?php

namespace Accordia\Cqrs\Aggregate;

abstract class AggregateRoot implements AggregateRootInterface
{
    /**
     * @var AggregateIdInterface
     */
    private $identifier;

    /**
     * @var AggregateRevision
     */
    private $revision;

    /**
     * @var DomainEventList
     */
    private $trackedEvents;

    /**
     * @param DomainEventList $history
     * @return AggregateRootInterface
     */
    public static function reconstituteFromHistory(DomainEventList $history): AggregateRootInterface
    {
        $aggRoot = new static;
        foreach ($history as $eventOccured) {
            $aggRoot = $aggRoot->reflectThat($eventOccured, false);
        }
        return $aggRoot;
    }

    /**
     * @return AggregateIdInterface
     */
    public function getIdentifier(): AggregateIdInterface
    {
        return $this->identifier;
    }

    /**
     * @return AggregateRevision
     */
    public function getRevision(): AggregateRevision
    {
        return $this->revision;
    }

    /**
     * @return DomainEventList
     */
    public function getTrackedEvents(): DomainEventList
    {
        return $this->trackedEvents;
    }

    /**
     * @return AggregateRootInterface
     */
    public function markClean(): AggregateRootInterface
    {
        $aggRoot = clone $this;
        $aggRoot->trackedEvents = DomainEventList::makeEmpty();
        return $aggRoot;
    }

    /**
     * @param AggregateIdInterface $aggregateId
     */
    protected function __construct(AggregateIdInterface $aggregateId)
    {
        $this->identifier = $aggregateId;
        $this->revision = AggregateRevision::makeEmpty();
        $this->trackedEvents = DomainEventList::makeEmpty();
    }

    /**
     * @param DomainEventInterface $eventOccured
     * @param bool $track
     * @return AggregateRoot
     */
    protected function reflectThat(DomainEventInterface $eventOccured, bool $track = true): self
    {
        $aggRoot = clone $this;
        if ($track) {
            $aggRoot->revision = $aggRoot->revision->increment();
            $eventOccured = $eventOccured->withAggregateRevision($aggRoot->revision);
            $aggRoot->trackedEvents = $aggRoot->trackedEvents->push($eventOccured);
        } else {
            $expectedAggregateRevision = $aggRoot->revision->increment();
            if (!$expectedAggregateRevision->equals($eventOccured->getAggregateRevision())) {
                throw new \Exception(sprintf(
                    "Given event-revision %s does not match expected AR revision at %s",
                    $eventOccured->getAggregateRevision(),
                    $expectedAggregateRevision
                ));
            }
            $aggRoot->revision = $expectedAggregateRevision;
        }
        $aggRoot->invokeEventHandler($eventOccured);
        return $aggRoot;
    }

    /**
     * @param DomainEventInterface $event
     * @throws \Exception
     */
    private function invokeEventHandler(DomainEventInterface $event)
    {
        $handlerName = preg_replace("/Event$/", "", (new \ReflectionClass($event))->getShortName());
        $handlerMethod = "when".ucfirst($handlerName);
        $handler = [ $this, $handlerMethod ];
        if (!is_callable($handler)) {
            throw new \Exception("Handler '$handlerMethod' isn't callable on ".static::class);
        }
        call_user_func($handler, $event);
    }
}
