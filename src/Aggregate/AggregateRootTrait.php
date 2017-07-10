<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\Aggregate;

trait AggregateRootTrait
{
    /** @var AggregateIdInterface */
    private $identifier;

    /** @var AggregateRevision */
    private $revision;

    /** @var DomainEventSequence */
    private $trackedEvents;

    public static function reconstituteFromHistory(
        AggregateIdInterface $aggregateId,
        DomainEventSequence $history
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

    public function getTrackedEvents(): DomainEventSequence
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
