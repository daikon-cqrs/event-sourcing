<?php

namespace Accordia\Cqrs\Aggregate;

interface AggregateRootInterface
{
    /**
     * @param DomainEventList $history
     * @return AggregateRootInterface
     */
    public static function reconstituteFromHistory(DomainEventList $history): AggregateRootInterface;

    /**
     * @return AggregateIdInterface
     */
    public function getIdentifier(): AggregateIdInterface;

    /**
     * @return AggregateRevision
     */
    public function getRevision(): AggregateRevision;

    /**
     * @return DomainEventList
     */
    public function getTrackedEvents(): DomainEventList;

    /**
     * @return AggregateRootInterface
     */
    public function markClean(): AggregateRootInterface;
}
