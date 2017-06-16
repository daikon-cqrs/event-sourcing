<?php

namespace Accordia\Cqrs\EventStore;

use Accordia\MessageBus\MessageInterface;
use Accordia\MessageBus\Metadata\Metadata;
use Accordia\Cqrs\Aggregate\DomainEventList;
use Accordia\Cqrs\Aggregate\Revision;

interface CommitInterface extends MessageInterface
{
    /**
     * @param StreamId $streamId
     * @param Revision $streamRevision
     * @param DomainEventList $eventLog
     * @param Metadata $metadata
     * @return CommitInterface
     */
    public static function make(
        StreamId $streamId,
        Revision $streamRevision,
        DomainEventList $eventLog,
        Metadata $metadata
    ): CommitInterface;

    /**
     * @return \Accordia\Cqrs\EventStore\StreamId
     */
    public function getStreamId(): StreamId;

    /**
     * @return Revision
     */
    public function getStreamRevision(): Revision;

    /**
     * @return Revision
     */
    public function getAggregateRevision(): Revision;

    /**
     * @return DomainEventList
     */
    public function getEventLog(): DomainEventList;

    /**
     * @return Metadata
     */
    public function getMetadata(): Metadata;
}
