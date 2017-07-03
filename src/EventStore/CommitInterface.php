<?php

namespace Daikon\Cqrs\EventStore;

use Daikon\MessageBus\MessageInterface;
use Daikon\MessageBus\Metadata\Metadata;
use Daikon\Cqrs\Aggregate\DomainEventSequence;
use Daikon\Cqrs\Aggregate\AggregateRevision;

interface CommitInterface extends MessageInterface
{
    public static function make(
        CommitStreamId $streamId,
        CommitStreamRevision $streamRevision,
        DomainEventSequence $eventLog,
        Metadata $metadata
    ): CommitInterface;

    public function getStreamId(): CommitStreamId;

    public function getStreamRevision(): CommitStreamRevision;

    public function getAggregateRevision(): AggregateRevision;

    public function getEventLog(): DomainEventSequence;

    public function getMetadata(): Metadata;
}
