<?php

namespace Accordia\Cqrs\EventStore;

use Accordia\MessageBus\MessageInterface;
use Accordia\MessageBus\Metadata\Metadata;
use Accordia\Cqrs\Aggregate\DomainEventList;
use Accordia\Cqrs\Aggregate\AggregateRevision;

final class Commit implements CommitInterface
{
    /**
     * @var CommitStreamId
     */
    private $streamId;

    /**
     * @var CommitStreamRevision
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
            CommitStreamId::fromNative($state["streamId"]),
            CommitStreamRevision::fromNative((int)$state["streamRevision"]),
            DomainEventList::fromArray($state["eventLog"]),
            Metadata::fromArray($state["metadata"])
        );
    }

    /**
     * @return CommitStreamId
     */
    public function getStreamId(): CommitStreamId
    {
        return $this->streamId;
    }

    /**
     * @return CommitStreamRevision
     */
    public function getStreamRevision(): CommitStreamRevision
    {
        return $this->streamRevision;
    }

    /**
     * @return AggregateRevision
     */
    public function getAggregateRevision(): AggregateRevision
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
     * @param CommitStreamId $streamId
     * @param CommitStreamRevision $streamRevision
     * @param DomainEventList $eventLog
     * @param Metadata $metadata
     */
    private function __construct(
        CommitStreamId $streamId,
        CommitStreamRevision $streamRevision,
        DomainEventList $eventLog,
        Metadata $metadata
    ) {
        $this->streamId = $streamId;
        $this->streamRevision = $streamRevision;
        $this->eventLog = $eventLog;
        $this->metadata = $metadata;
    }
}
