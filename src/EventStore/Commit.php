<?php

namespace Daikon\Cqrs\EventStore;

use Daikon\MessageBus\MessageInterface;
use Daikon\MessageBus\Metadata\Metadata;
use Daikon\Cqrs\Aggregate\DomainEventSequence;
use Daikon\Cqrs\Aggregate\AggregateRevision;

final class Commit implements CommitInterface
{
    private $streamId;

    private $streamRevision;

    private $eventLog;

    private $metadata;

    public static function make(
        CommitStreamId $streamId,
        CommitStreamRevision $streamRevision,
        DomainEventSequence $eventLog,
        Metadata $metadata
    ): CommitInterface {
        return new static($streamId, $streamRevision, $eventLog, $metadata);
    }

    public static function fromArray(array $state): MessageInterface
    {
        return new static(
            CommitStreamId::fromNative($state["streamId"]),
            CommitStreamRevision::fromNative((int)$state["streamRevision"]),
            DomainEventSequence::fromArray($state["eventLog"]),
            Metadata::fromArray($state["metadata"])
        );
    }

    public function getStreamId(): CommitStreamId
    {
        return $this->streamId;
    }

    public function getStreamRevision(): CommitStreamRevision
    {
        return $this->streamRevision;
    }

    public function getAggregateRevision(): AggregateRevision
    {
        return $this->eventLog->getHeadRevision();
    }

    public function getEventLog(): DomainEventSequence
    {
        return $this->eventLog;
    }

    public function getMetadata(): Metadata
    {
        return $this->metadata;
    }

    public function toArray(): array
    {
        return [
            "streamId" => $this->streamId->toNative(),
            "streamRevision" => $this->streamRevision->toNative(),
            "eventLog" => $this->eventLog->toNative(),
            "metadata" => $this->metadata->toArray()
        ];
    }

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
