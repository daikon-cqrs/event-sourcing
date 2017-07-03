<?php

namespace Daikon\Cqrs\Aggregate;

interface AggregateRootInterface
{
    public static function reconstituteFromHistory(DomainEventSequence $history): AggregateRootInterface;

    public function getIdentifier(): AggregateIdInterface;

    public function getRevision(): AggregateRevision;

    public function getTrackedEvents(): DomainEventSequence;

    public function markClean(): AggregateRootInterface;
}
