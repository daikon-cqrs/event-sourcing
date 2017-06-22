<?php

namespace Accordia\Cqrs\EventStore;

use Accordia\MessageBus\MessageInterface;
use Accordia\MessageBus\Metadata\Metadata;
use Accordia\Cqrs\Aggregate\DomainEventSequence;
use Accordia\Cqrs\Aggregate\AggregateRevision;

interface CommitInterface extends MessageInterface
{
    /**
     * @param CommitStreamId $streamId
     * @param CommitStreamRevision $streamRevision
     * @param DomainEventSequence $eventLog
     * @param Metadata $metadata
     * @return CommitInterface
     */
    public static function make(
        CommitStreamId $streamId,
        CommitStreamRevision $streamRevision,
        DomainEventSequence $eventLog,
        Metadata $metadata
    ): CommitInterface;

    /**
     * @return \Accordia\Cqrs\EventStore\CommitStreamId
     */
    public function getStreamId(): CommitStreamId;

    /**
     * @return CommitStreamRevision
     */
    public function getStreamRevision(): CommitStreamRevision;

    /**
     * @return AggregateRevision
     */
    public function getAggregateRevision(): AggregateRevision;

    /**
     * @return DomainEventSequence
     */
    public function getEventLog(): DomainEventSequence;

    /**
     * @return Metadata
     */
    public function getMetadata(): Metadata;
}
