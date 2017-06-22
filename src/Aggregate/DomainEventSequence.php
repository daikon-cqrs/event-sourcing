<?php

namespace Accordia\Cqrs\Aggregate;

use Countable;
use Ds\Vector;
use Iterator;
use IteratorAggregate;

final class DomainEventSequence implements IteratorAggregate, Countable
{
    /**
     * @var Vector
     */
    private $compositeVector;

    /**
     * @param array $eventsArray
     * @return DomainEventSequence
     */
    public static function fromArray(array $eventsArray): DomainEventSequence
    {
        return new static(array_map(function (array $eventState) {
            $eventFqcn = $this->resolveEventFqcn($eventState);
            return $eventFqcn::fromArray($eventState);
        }, $eventsArray));
    }

    /**
     * @return DomainEventSequence
     */
    public static function makeEmpty(): DomainEventSequence
    {
        return new self;
    }

    /**
     * @param DomainEventInterface[] $events
     */
    public function __construct(array ...$events)
    {
        (function (DomainEventInterface ...$events) {
            $this->compositeVector = new Vector($events);
        })(...$events);
    }

    /**
     * @param  DomainEventInterface $event
     * @return DomainEventSequence
     */
    public function push(DomainEventInterface $event): self
    {
        if (!$this->isEmpty() && !$this->getHeadRevision()->increment()->equals($event->getAggregateRevision())) {
            throw new \Exception(sprintf(
                "Trying to add unexpected revision %s to event-sequence. Expected revision is $nextRevision",
                $event->getAggregateRevision()
            ));
        }
        $eventSequence = clone $this;
        $eventSequence->compositeVector->push($event);
        return $eventSequence;
    }

    /**
     * @return mixed[]
     */
    public function toNative(): array
    {
        $nativeList = [];
        foreach ($this as $event) {
            $nativeRep = $event->toArray();
            $nativeRep["@type"] = get_class($event);
            $nativeList[] = $nativeRep;
        }
        return $nativeList;
    }

    /**
     * @return AggregateRevision
     */
    public function getHeadRevision(): AggregateRevision
    {
        return $this->isEmpty() ? AggregateRevision::makeEmpty() : $this->getHead()->getAggregateRevision();
    }

    /**
     * @return AggregateRevision
     */
    public function getTailRevision(): AggregateRevision
    {
        return $this->isEmpty() ? AggregateRevision::makeEmpty() :$this->getTail()->getAggregateRevision();
    }

    /**
     * @return DomainEventInterface|null
     */
    public function getTail(): ?DomainEventInterface
    {
        return $this->compositeVector->first();
    }

    /**
     * @return DomainEventInterface|null
     */
    public function getHead(): ?DomainEventInterface
    {
        return $this->compositeVector->last();
    }

    /**
     * @return int
     */
    public function getLength(): int
    {
        return $this->count();
    }

    /**
     * @return boolean
     */
    public function isEmpty(): bool
    {
        return $this->compositeVector->isEmpty();
    }

    /**
     * @param DomainEventInterface $event
     * @return int
     */
    public function indexOf(DomainEventInterface $event): int
    {
        return $this->compositeVector->find($event);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->compositeVector->count();
    }

    /**
     * @return Iterator
     */
    public function getIterator(): Iterator
    {
        return $this->compositeVector->getIterator();
    }

    /**
     * @param array $eventState
     * @return string
     * @throws \Exception
     */
    private static function resolveEventFqcn(array $eventState): DomainEventInterface
    {
        if (!isset($eventState["@type"])) {
            throw new \Exception("Missing expected typeinfo for event at key '@type' within given state-array.");
        }
        $eventFqcn = $eventState["@type"];
        if (!class_exists($eventFqcn)) {
            throw new \Exception("Can't load event-class '$eventFqcn' given within state-array.");
        }
        return $eventFqcn;
    }

    private function __clone()
    {
        $this->compositeVector = clone $this->compositeVector;
    }
}
