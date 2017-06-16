<?php

namespace Accordia\Cqrs\Aggregate;

use Accordia\DataStructures\TypedListTrait;

class DomainEventList implements \IteratorAggregate, \Countable
{
    use TypedListTrait;

    /**
     * @param array $state
     * @return DomainEventList
     */
    public static function fromArray(array $state): DomainEventList
    {
        return new static(array_map([ static::class, "restoreEvent" ], $state));
    }

    /**
     * DomainEventList constructor.
     * @param array $events
     */
    public function __construct(array $events = [])
    {
        $this->init($events, DomainEventInterface::class);
    }

    /**
     * @return array
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
     * @return Revision
     */
    public function getHeadRevision(): Revision
    {
        return $this->compositeVector->last()->getAggregateRevision();
    }

    /**
     * @return Revision
     */
    public function getTailRevision(): Revision
    {
        return $this->compositeVector->first()->getAggregateRevision();
    }

    /**
     * @return DomainEventInterface
     */
    public function getFirst(): DomainEventInterface
    {
        return $this->compositeVector->first();
    }

    /**
     * @param array $eventState
     * @return DomainEventInterface
     * @throws \Exception
     */
    private static function restoreEvent(array $eventState): DomainEventInterface
    {
        if (!isset($eventState["@type"])) {
            throw new \Exception("Missing expected typeinfo for event at key '@type' within given state-array.");
        }
        $eventFqcn = $eventState["@type"];
        if (!class_exists($eventFqcn)) {
            throw new \Exception("Can't load event-class '$eventFqcn' given within state-array.");
        }
        if (!is_subclass_of($eventFqcn, DomainEventInterface::class)) {
            throw new \Exception(sprintf(
                "Given event-class '%s' doesn't implement required interface '%s'",
                $eventFqcn,
                DomainEventInterface::class
            ));
        }
        return $eventFqcn::fromArray($eventState);
    }
}
