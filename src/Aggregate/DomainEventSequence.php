<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Cqrs\Aggregate;

use Countable;
use Ds\Vector;
use Iterator;
use IteratorAggregate;

final class DomainEventSequence implements IteratorAggregate, Countable
{
    private $compositeVector;

    public static function fromArray(array $eventsArray): DomainEventSequence
    {
        return new static(array_map(function (array $eventState) {
            $eventFqcn = $this->resolveEventFqcn($eventState);
            return $eventFqcn::fromArray($eventState);
        }, $eventsArray));
    }

    public static function makeEmpty(): DomainEventSequence
    {
        return new self;
    }

    public function __construct(array $events = [])
    {
        (function (DomainEventInterface ...$events) {
            $this->compositeVector = new Vector($events);
        })(...$events);
    }

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

    public function getHeadRevision(): AggregateRevision
    {
        return $this->isEmpty() ? AggregateRevision::makeEmpty() : $this->getHead()->getAggregateRevision();
    }

    public function getTailRevision(): AggregateRevision
    {
        return $this->isEmpty() ? AggregateRevision::makeEmpty() :$this->getTail()->getAggregateRevision();
    }

    public function getTail(): ?DomainEventInterface
    {
        return $this->compositeVector->first();
    }

    public function getHead(): ?DomainEventInterface
    {
        return $this->compositeVector->last();
    }

    public function getLength(): int
    {
        return $this->count();
    }

    public function isEmpty(): bool
    {
        return $this->compositeVector->isEmpty();
    }

    public function indexOf(DomainEventInterface $event): int
    {
        return $this->compositeVector->find($event);
    }

    public function count(): int
    {
        return $this->compositeVector->count();
    }

    public function getIterator(): Iterator
    {
        return $this->compositeVector->getIterator();
    }

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
