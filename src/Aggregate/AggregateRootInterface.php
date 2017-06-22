<?php

namespace Accordia\Cqrs\Aggregate;

interface AggregateRootInterface
{
    /**
     * @param DomainEventSequence $history
     * @return AggregateRootInterface
     */
    public static function reconstituteFromHistory(DomainEventSequence $history): AggregateRootInterface;

    /**
     * @return AggregateIdInterface
     */
    public function getIdentifier(): AggregateIdInterface;

    /**
     * @return AggregateRevision
     */
    public function getRevision(): AggregateRevision;

    /**
     * @return DomainEventSequence
     */
    public function getTrackedEvents(): DomainEventSequence;

    /**
     * @return AggregateRootInterface
     */
    public function markClean(): AggregateRootInterface;
}
