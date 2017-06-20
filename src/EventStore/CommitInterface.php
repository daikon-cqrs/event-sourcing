<?php

namespace Accordia\Cqrs\EventStore;

use Accordia\MessageBus\MessageInterface;
use Accordia\MessageBus\Metadata\Metadata;
use Accordia\Cqrs\Aggregate\DomainEventList;
use Accordia\Cqrs\Aggregate\AggregateRevision;

interface CommitInterface extends MessageInterface
{
    /**
     * @param CommitStreamId $streamId
     * @param CommitStreamRevision $streamRevision
     * @param DomainEventList $eventLog
     * @param Metadata $metadata
     * @return CommitInterface
     */
    public static function make(
        CommitStreamId $streamId,
        CommitStreamRevision $streamRevision,
        DomainEventList $eventLog,
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
     * @return DomainEventList
     */
    public function getEventLog(): DomainEventList;

    /**
     * @return Metadata
     */
    public function getMetadata(): Metadata;
}
