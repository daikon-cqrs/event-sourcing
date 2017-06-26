<?php

namespace Daikon\Cqrs\EventStore;

use Daikon\MessageBus\Metadata\Metadata;
use Daikon\Cqrs\Aggregate\DomainEventSequence;
use Daikon\Cqrs\Aggregate\AggregateRevision;

interface CommitStreamInterface extends \IteratorAggregate, \Countable
{
    /**
     * @param CommitStreamId $streamId
     * @param string $commitImplementor
     * @return CommitStreamInterface
     */
    public static function fromStreamId(
        CommitStreamId $streamId,
        string $commitImplementor = Commit::class
    ): CommitStreamInterface;

    /**
     * @param DomainEventSequence $eventLog
     * @param Metadata $metadata
     * @return CommitStreamInterface
     */
    public function appendEvents(DomainEventSequence $eventLog, Metadata $metadata): CommitStreamInterface;

    /**
     * @param CommitInterface $commit
     * @return CommitStreamInterface
     */
    public function appendCommit(CommitInterface $commit): CommitStreamInterface;

    /**
     * @param CommitStreamRevision $fromRev
     * @param CommitStreamRevision|null $toRev
     * @return CommitSequence
     */
    public function getCommitRange(CommitStreamRevision $fromRev, CommitStreamRevision $toRev = null): CommitSequence;

    /**
     * @return CommitStreamId
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
     * @return CommitInterface|null
     */
    public function getHead(): ?CommitInterface;
}
