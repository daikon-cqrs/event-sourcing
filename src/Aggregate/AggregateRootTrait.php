<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\EventSourcing\Aggregate;

use Assert\Assertion;
use Daikon\EventSourcing\Aggregate\Event\DomainEventInterface;
use Daikon\EventSourcing\Aggregate\Event\DomainEventSequence;
use Daikon\EventSourcing\Aggregate\Event\DomainEventSequenceInterface;
use Daikon\Interop\RuntimeException;
use ReflectionClass;

trait AggregateRootTrait
{
    private AggregateIdInterface $identifier;

    private AggregateRevision $revision;

    private DomainEventSequenceInterface $trackedEvents;

    public static function reconstituteFromHistory(
        AggregateIdInterface $aggregateId,
        DomainEventSequenceInterface $history
    ): self {
        $aggregateRoot = new static($aggregateId);
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

    protected function __construct(AggregateIdInterface $aggregateId)
    {
        $this->identifier = $aggregateId;
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
            'Given event revision %s does not match expected AR revision at %s',
            (string)$event->getAggregateRevision(),
            (string)$expectedRevision
        ));
    }

    private function assertExpectedIdentifier(DomainEventInterface $event, AggregateIdInterface $expectedId): void
    {
        Assertion::true($expectedId->equals($event->getAggregateId()), sprintf(
            'Given event identifier %s does not match expected AR identifier at %s',
            (string)$event->getAggregateId(),
            (string)$expectedId
        ));
    }

    private function invokeEventHandler(DomainEventInterface $event): void
    {
        $handlerName = preg_replace('/Event$/', '', (new ReflectionClass($event))->getShortName());
        $handlerMethod = 'when'.ucfirst($handlerName);
        $handler = [ $this, $handlerMethod ];
        if (!is_callable($handler)) {
            throw new RuntimeException(sprintf('Handler "%s" is not callable on '.static::class, $handlerMethod));
        }
        call_user_func($handler, $event);
    }
}
