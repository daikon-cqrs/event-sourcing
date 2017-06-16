<?php

namespace Accordia\Cqrs\Aggregate;

abstract class AggregateRoot implements AggregateRootInterface
{
    /**
     * @var Revision
     */
    private $revision;

    /**
     * @var DomainEventList
     */
    private $trackedEvents;

    /**
     * @param DomainEventList $history
     * @return AggregateRootInterface
     */
    public static function reconstituteFromHistory(DomainEventList $history): AggregateRootInterface
    {
        $aggRoot = new static;
        foreach ($history as $passedEventHappened) {
            $aggRoot = $aggRoot->reflectThat($passedEventHappened, false);
        }
        return $aggRoot;
    }

    /**
     * @return Revision
     */
    public function getRevision(): Revision
    {
        return $this->revision;
    }

    /**
     * @return DomainEventList
     */
    public function getTrackedEvents(): DomainEventList
    {
        return $this->trackedEvents;
    }

    /**
     * @return AggregateRootInterface
     */
    public function markClean(): AggregateRootInterface
    {
        $aggRoot = clone $this;
        $aggRoot->trackedEvents = new DomainEventList;
        return $aggRoot;
    }

    protected function __construct()
    {
        $this->revision = Revision::makeEmpty();
        $this->trackedEvents = new DomainEventList;
    }

    /**
     * @param DomainEventInterface $somethingHappened
     * @param bool $track
     * @return AggregateRoot
     */
    protected function reflectThat(DomainEventInterface $somethingHappened, bool $track = true): self
    {
        $aggRoot = clone $this;
        $aggRoot->invokeEventHandler($somethingHappened);
        $aggRoot->revision = $this->revision->increment();
        if ($track) {
            $aggRoot->trackedEvents = $this->trackedEvents->push(
                $somethingHappened->withAggregateRevision($aggRoot->getRevision())
            );
        }
        return $aggRoot;
    }

    /**
     * @param DomainEventInterface $event
     * @throws \Exception
     */
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
