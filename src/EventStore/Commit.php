<?php

namespace Accordia\Cqrs\EventStore;

use Accordia\MessageBus\MessageInterface;
use Accordia\MessageBus\Metadata\Metadata;
use Accordia\Cqrs\Aggregate\DomainEventList;
use Accordia\Cqrs\Aggregate\Revision;

class Commit implements CommitInterface
{
    /**
     * @var StreamId
     */
    private $streamId;

    /**
     * @var Revision
     */
    private $streamRevision;

    /**
     * @var DomainEventList
     */
    private $eventLog;

    /**
     * @var Metadata
     */
    private $metadata;

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
    ): CommitInterface {
        return new static($streamId, $streamRevision, $eventLog, $metadata);
    }

    /**
     * @param array $state
     * @return MessageInterface
     */
    public static function fromArray(array $state): MessageInterface
    {
        return new static(
            StreamId::fromNative($state["streamId"]),
            Revision::fromNative((int)$state["streamRevision"]),
            DomainEventList::fromArray($state["eventLog"]),
            Metadata::fromArray($state["metadata"])
        );
    }

    /**
     * @return StreamId
     */
    public function getStreamId(): StreamId
    {
        return $this->streamId;
    }

    /**
     * @return Revision
     */
    public function getStreamRevision(): Revision
    {
        return $this->streamRevision;
    }

    /**
     * @return Revision
     */
    public function getAggregateRevision(): Revision
    {
        return $this->eventLog->getHeadRevision();
    }

    /**
     * @return DomainEventList
     */
    public function getEventLog(): DomainEventList
    {
        return $this->eventLog;
    }

    /**
     * @return Metadata
     */
    public function getMetadata(): Metadata
    {
        return $this->metadata;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            "streamId" => $this->streamId->toNative(),
            "streamRevision" => $this->streamRevision->toNative(),
            "eventLog" => $this->eventLog->toNative(),
            "metadata" => $this->metadata->toArray()
        ];
    }

    /**
     * @param StreamId $streamId
     * @param Revision $streamRevision
     * @param DomainEventList $eventLog
     * @param Metadata $metadata
     */
    private function __construct(
        StreamId $streamId,
        Revision $streamRevision,
        DomainEventList $eventLog,
        Metadata $metadata
    ) {
        $this->streamId = $streamId;
        $this->streamRevision = $streamRevision;
        $this->eventLog = $eventLog;
        $this->metadata = $metadata;
    }
}
