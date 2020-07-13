<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\EventSourcing\Aggregate;

use Daikon\EventSourcing\Aggregate\Event\DomainEventInterface;
use Daikon\EventSourcing\Aggregate\Event\DomainEventSequence;
use Daikon\EventSourcing\Aggregate\Event\DomainEventSequenceInterface;
use Daikon\Interop\Assertion;
use Daikon\Interop\RuntimeException;
use ReflectionClass;

abstract class AggregateRoot implements AggregateRootInterface
{
    private AggregateIdInterface $identifier;

    private AggregateRevision $revision;

    private DomainEventSequenceInterface $trackedEvents;

    /** @return static */
    public static function reconstituteFromHistory(
        AggregateIdInterface $identifier,
        DomainEventSequenceInterface $history
    ): self {
        $aggregateRoot = new static($identifier);
        foreach ($history as $historicalEvent) {
            $aggregateRoot = $aggregateRoot->reconstitute($historicalEvent);
        }
        return $aggregateRoot;
    }

    public function getIdentifier(): AggregateIdInterface
    {
        return $this->identifier;
    }

    public function getRevision(): AggregateRevision
    {
        return $this->revision;
    }

    public function getTrackedEvents(): DomainEventSequenceInterface
    {
        return $this->trackedEvents;
    }

    protected function __construct(AggregateIdInterface $identifier)
    {
        $this->identifier = $identifier;
        $this->revision = AggregateRevision::makeEmpty();
        $this->trackedEvents = DomainEventSequence::makeEmpty();
    }

    protected function reflectThat(DomainEventInterface $eventOccurred): self
    {
        $this->assertExpectedIdentifier($eventOccurred, $this->getIdentifier());
        $aggregateRoot = clone $this;
        $aggregateRoot->revision = $aggregateRoot->revision->increment();
        $eventOccurred = $eventOccurred->withAggregateRevision($aggregateRoot->revision);
        $aggregateRoot->trackedEvents = $aggregateRoot->trackedEvents->push($eventOccurred);
        $aggregateRoot->invokeEventHandler($eventOccurred);
        return $aggregateRoot;
    }

    private function reconstitute(DomainEventInterface $historicalEvent): self
    {
        $this->assertExpectedIdentifier($historicalEvent, $this->getIdentifier());
        $aggregateRoot = clone $this;
        $expectedAggregateRevision = $aggregateRoot->revision->increment();
        $this->assertExpectedRevision($historicalEvent, $expectedAggregateRevision);
        $aggregateRoot->revision = $expectedAggregateRevision;
        $aggregateRoot->invokeEventHandler($historicalEvent);
        return $aggregateRoot;
    }

    private function assertExpectedRevision(DomainEventInterface $event, AggregateRevision $expectedRevision): void
    {
        Assertion::true($expectedRevision->equals($event->getAggregateRevision()), sprintf(
            "Given event revision '%s' does not match expected AR revision at '%s'.",
            (string)$event->getAggregateRevision(),
            (string)$expectedRevision
        ));
    }

    private function assertExpectedIdentifier(DomainEventInterface $event, AggregateIdInterface $expectedId): void
    {
        Assertion::true($expectedId->equals($event->getAggregateId()), sprintf(
            "Given event identifier '%s' does not match expected AR identifier at '%s'.",
            (string)$event->getAggregateId(),
            (string)$expectedId
        ));
    }

    private function invokeEventHandler(DomainEventInterface $event): void
    {
        $handlerName = (new ReflectionClass($event))->getShortName();
        $handlerMethod = 'when'.ucfirst($handlerName);
        $handler = [$this, $handlerMethod];
        if (!is_callable($handler)) {
            throw new RuntimeException(
                sprintf("Handler '%s' is not callable on '%s'.", $handlerMethod, static::class)
            );
        }
        $handler($event);
    }
}
