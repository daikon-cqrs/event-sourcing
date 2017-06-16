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
     * @return Revision
     */
    public function getRevision(): Revision;

    /**
     * @return DomainEventList
     */
    public function getTrackedEvents(): DomainEventList;

    /**
     * @return AggregateRootInterface
     */
    public function markClean(): AggregateRootInterface;
}
