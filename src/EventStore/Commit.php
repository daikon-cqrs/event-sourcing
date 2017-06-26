<?php

namespace Daikon\Cqrs\EventStore;

use Daikon\MessageBus\MessageInterface;
use Daikon\MessageBus\Metadata\Metadata;
use Daikon\Cqrs\Aggregate\DomainEventSequence;
use Daikon\Cqrs\Aggregate\AggregateRevision;

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
     * @var DomainEventSequence
     */
    private $eventLog;

    /**
     * @var Metadata
     */
    private $metadata;

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
            DomainEventSequence::fromArray($state["eventLog"]),
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
     * @return DomainEventSequence
     */
    public function getEventLog(): DomainEventSequence
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
     * @param DomainEventSequence $eventLog
     * @param Metadata $metadata
     */
    private function __construct(
        CommitStreamId $streamId,
        CommitStreamRevision $streamRevision,
        DomainEventSequence $eventLog,
        Metadata $metadata
    ) {
        $this->streamId = $streamId;
        $this->streamRevision = $streamRevision;
        $this->eventLog = $eventLog;
        $this->metadata = $metadata;
    }
}
