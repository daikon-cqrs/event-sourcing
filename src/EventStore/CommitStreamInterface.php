<?php

namespace Daikon\Cqrs\EventStore;

use Daikon\MessageBus\Metadata\Metadata;
use Daikon\Cqrs\Aggregate\DomainEventSequence;
use Daikon\Cqrs\Aggregate\AggregateRevision;

interface CommitStreamInterface extends \IteratorAggregate, \Countable
{
    public static function fromStreamId(
        CommitStreamId $streamId,
        string $commitImplementor = Commit::class
    ): CommitStreamInterface;

    public function appendEvents(DomainEventSequence $eventLog, Metadata $metadata): CommitStreamInterface;

    public function appendCommit(CommitInterface $commit): CommitStreamInterface;

    public function getCommitRange(CommitStreamRevision $fromRev, CommitStreamRevision $toRev = null): CommitSequence;

    public function getStreamId(): CommitStreamId;

    public function getStreamRevision(): CommitStreamRevision;

    public function getAggregateRevision(): AggregateRevision;

    public function getHead(): ?CommitInterface;
}
