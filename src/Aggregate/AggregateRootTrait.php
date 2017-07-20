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
        foreach ($history as $eventOccured) {
            $aggRoot = $aggRoot->reflectThat($eventOccured, false);
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

    public function markClean(): AggregateRootInterface
    {
        $aggRoot = clone $this;
        $aggRoot->trackedEvents = DomainEventSequence::makeEmpty();
        return $aggRoot;
    }

    protected function __construct(AggregateIdInterface $aggregateId)
    {
        $this->identifier = $aggregateId;
        $this->revision = AggregateRevision::makeEmpty();
        $this->trackedEvents = DomainEventSequence::makeEmpty();
    }

    protected function reflectThat(DomainEventInterface $eventOccured, bool $track = true): AggregateRootInterface
    {
        $this->assertExpectedIdentifier($eventOccured, $this->getIdentifier());
        $aggRoot = clone $this;
        if ($track) {
            $aggRoot->revision = $aggRoot->revision->increment();
            $eventOccured = $eventOccured
                ->withAggregateRevision($aggRoot->revision);
            $aggRoot->trackedEvents = $aggRoot->trackedEvents->push($eventOccured);
        } else {
            $expectedAggregateRevision = $aggRoot->revision->increment();
            $this->assertExpectedRevision($eventOccured, $expectedAggregateRevision);
            $aggRoot->revision = $expectedAggregateRevision;
        }
        $aggRoot->invokeEventHandler($eventOccured);
        return $aggRoot;
    }

    private function assertExpectedRevision(DomainEventInterface $event, AggregateRevision $expectedRevision): void
    {
        if (!$expectedRevision->equals($event->getAggregateRevision())) {
            throw new \Exception(sprintf(
                'Given event-revision %s does not match expected AR revision at %s',
                $event->getAggregateRevision(),
                $expectedRevision
            ));
        }
    }

    private function assertExpectedIdentifier(DomainEventInterface $event, AggregateIdInterface $expectedId): void
    {
        if (!$expectedId->equals($event->getAggregateId())) {
            throw new \Exception(sprintf(
                'Given event-identifier %s does not match expected AR identifier at %s',
                $event->getAggregateId(),
                $expectedId
            ));
        }
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
