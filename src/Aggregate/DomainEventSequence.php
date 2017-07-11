<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\Aggregate;

use Ds\Vector;
use Iterator;

final class DomainEventSequence implements DomainEventSequenceInterface
{
    /** @var Vector */
    private $compositeVector;

    public static function fromArray(array $eventsArray): DomainEventSequenceInterface
    {
        return new static(array_map(function (array $eventState): DomainEventInterface {
            $eventFqcn = self::resolveEventFqcn($eventState);
            return call_user_func([ $eventFqcn, 'fromArray' ], $eventState);
        }, $eventsArray));
    }

    public static function makeEmpty(): DomainEventSequenceInterface
    {
        return new self;
    }

    public function __construct(array $events = [])
    {
        $this->compositeVector = (function (DomainEventInterface ...$events): Vector {
            return new Vector($events);
        })(...$events);
    }

    public function push(DomainEventInterface $event): DomainEventSequenceInterface
    {
        $expectedRevision = $this->getHeadRevision()->increment();
        if (!$this->isEmpty() && !$expectedRevision->equals($event->getAggregateRevision())) {
            throw new \Exception(sprintf(
                'Trying to add unexpected revision %s to event-sequence. Expected revision is %s',
                $event->getAggregateRevision(),
                $expectedRevision
            ));
        }
        $eventSequence = clone $this;
        $eventSequence->compositeVector->push($event);
        return $eventSequence;
    }

    public function append(DomainEventSequenceInterface $events): DomainEventSequenceInterface
    {
        $eventSequence = clone $this;
        foreach ($events as $event) {
            $eventSequence = $eventSequence->push($event);
        }
        return $eventSequence;
    }

    public function toNative(): array
    {
        $nativeList = [];
        foreach ($this as $event) {
            $nativeRep = $event->toArray();
            $nativeRep['@type'] = get_class($event);
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
        return $this->isEmpty() ? AggregateRevision::makeEmpty() : $this->getTail()->getAggregateRevision();
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

    private static function resolveEventFqcn(array $eventState): string
    {
        if (!isset($eventState['@type'])) {
            throw new \Exception('Missing expected typeinfo for event at key "@type" within given state-array.');
        }
        $eventFqcn = $eventState['@type'];
        if (!class_exists($eventFqcn)) {
            throw new \Exception(sprintf('Can not load event-class "%s" given within state-array.', $eventFqcn));
        }
        return $eventFqcn;
    }

    private function __clone()
    {
        $this->compositeVector = clone $this->compositeVector;
    }
}
