<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\Aggregate;

use Assert\Assertion;
use Daikon\EventSourcing\Aggregate\Event\DomainEventInterface;
use Daikon\EventSourcing\Aggregate\Event\DomainEventSequence;
use Daikon\EventSourcing\Aggregate\Event\DomainEventSequenceInterface;

trait AggregateRootTrait
{
    /** @var AggregateIdInterface */
    private $identifier;

    /** @var AggregateRevision */
    private $revision;

    /** @var DomainEventSequenceInterface */
    private $trackedEvents;

    public static function reconstituteFromHistory(
        AggregateIdInterface $aggregateId,
        DomainEventSequenceInterface $history
    ): AggregateRootInterface {
        $aggRoot = new static($aggregateId);
        foreach ($history as $historicalEvent) {
            $aggRoot = $aggRoot->reconstitute($historicalEvent);
        }
        return $aggRoot;
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

    protected function reflectThat(DomainEventInterface $eventOccured): AggregateRootInterface
    {
        $this->assertExpectedIdentifier($eventOccured, $this->getIdentifier());
        $aggRoot = clone $this;
        $aggRoot->revision = $aggRoot->revision->increment();
        $eventOccured = $eventOccured->withAggregateRevision($aggRoot->revision);
        $aggRoot->trackedEvents = $aggRoot->trackedEvents->push($eventOccured);
        $aggRoot->invokeEventHandler($eventOccured);
        return $aggRoot;
    }

    private function reconstitute(DomainEventInterface $historicalEvent): AggregateRootInterface
    {
        $this->assertExpectedIdentifier($historicalEvent, $this->getIdentifier());
        $aggRoot = clone $this;
        $expectedAggregateRevision = $aggRoot->revision->increment();
        $this->assertExpectedRevision($historicalEvent, $expectedAggregateRevision);
        $aggRoot->revision = $expectedAggregateRevision;
        $aggRoot->invokeEventHandler($historicalEvent);
        return $aggRoot;
    }

    private function assertExpectedRevision(DomainEventInterface $event, AggregateRevision $expectedRevision): void
    {
        Assertion::true($expectedRevision->equals($event->getAggregateRevision()), sprintf(
            'Given event-revision %s does not match expected AR revision at %s',
            $event->getAggregateRevision(),
            $expectedRevision
        ));
    }

    private function assertExpectedIdentifier(DomainEventInterface $event, AggregateIdInterface $expectedId): void
    {
        Assertion::true($expectedId->equals($event->getAggregateId()), sprintf(
            'Given event-identifier %s does not match expected AR identifier at %s',
            $event->getAggregateId(),
            $expectedId
        ));
    }

    private function invokeEventHandler(DomainEventInterface $event): void
    {
        $handlerName = preg_replace('/Event$/', '', (new \ReflectionClass($event))->getShortName());
        $handlerMethod = 'when'.ucfirst($handlerName);
        $handler = [ $this, $handlerMethod ];
        if (!is_callable($handler)) {
            throw new \Exception(sprintf('Handler "%s" is not callable on '.static::class, $handlerMethod));
        }
        call_user_func($handler, $event);
    }
}
