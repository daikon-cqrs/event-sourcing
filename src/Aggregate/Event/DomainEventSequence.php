<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\EventSourcing\Aggregate\Event;

use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Ds\Vector;
use InvalidArgumentException;
use RuntimeException;

final class DomainEventSequence implements DomainEventSequenceInterface
{
    private Vector $compositeVector;

    /** @param array $events */
    public static function fromNative($events): self
    {
        return new self(array_map(function (array $state): DomainEventInterface {
            $eventFqcn = self::resolveEventFqcn($state);
            return call_user_func([$eventFqcn, 'fromNative'], $state);
        }, $events));
    }

    public static function makeEmpty(): self
    {
        return new self;
    }

    public function __construct(iterable $events = [])
    {
        $this->compositeVector = (
            fn(DomainEventInterface ...$events): Vector => new Vector($events)
        )(...$events);
    }

    public function push(DomainEventInterface $event): self
    {
        $expectedRevision = $this->getHeadRevision()->increment();
        if (!$this->isEmpty() && !$expectedRevision->equals($event->getAggregateRevision())) {
            throw new RuntimeException(sprintf(
                'Trying to add invalid revision %s to event-sequence, expected revision is %s',
                (string)$event->getAggregateRevision(),
                (string)$expectedRevision
            ));
        }
        $eventSequence = clone $this;
        $eventSequence->compositeVector->push($event);
        return $eventSequence;
    }

    public function append(DomainEventSequenceInterface $events): self
    {
        $eventSequence = $this;
        foreach ($events as $event) {
            $eventSequence = $eventSequence->push($event);
        }
        return $eventSequence;
    }

    public function resequence(AggregateRevision $aggregateRevision): self
    {
        $eventSequence = self::makeEmpty();
        foreach ($this as $event) {
            $aggregateRevision = $aggregateRevision->increment();
            $eventSequence->compositeVector->push($event->withAggregateRevision($aggregateRevision));
        }
        return $eventSequence;
    }

    public function toNative(): array
    {
        $nativeList = [];
        foreach ($this as $event) {
            $nativeRep = $event->toNative();
            $nativeRep['@type'] = get_class($event);
            $nativeList[] = $nativeRep;
        }
        return $nativeList;
    }

    public function getHeadRevision(): AggregateRevision
    {
        if ($this->isEmpty()) {
            return AggregateRevision::makeEmpty();
        }
        return $this->getHead()->getAggregateRevision();
    }

    public function getTailRevision(): AggregateRevision
    {
        if ($this->isEmpty()) {
            return AggregateRevision::makeEmpty();
        }
        return $this->getTail()->getAggregateRevision();
    }

    public function getTail(): DomainEventInterface
    {
        return $this->compositeVector->first();
    }

    public function getHead(): DomainEventInterface
    {
        return $this->compositeVector->last();
    }

    public function isEmpty(): bool
    {
        return $this->compositeVector->isEmpty();
    }

    public function indexOf(DomainEventInterface $event)
    {
        return $this->compositeVector->find($event);
    }

    public function count(): int
    {
        return $this->compositeVector->count();
    }

    public function getIterator(): Vector
    {
        return $this->compositeVector;
    }

    private static function resolveEventFqcn(array $eventState): string
    {
        if (!isset($eventState['@type'])) {
            throw new InvalidArgumentException("Missing expected key '@type' within given state array.");
        }
        $eventFqcn = $eventState['@type'];
        if (!class_exists($eventFqcn)) {
            throw new InvalidArgumentException("Cannot find event class '$eventFqcn' given within state array.");
        }
        return $eventFqcn;
    }

    private function __clone()
    {
        $this->compositeVector = clone $this->compositeVector;
    }
}
