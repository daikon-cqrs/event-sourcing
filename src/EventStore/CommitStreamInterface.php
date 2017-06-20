<?php

namespace Accordia\Cqrs\EventStore;

use Accordia\MessageBus\Metadata\Metadata;
use Accordia\Cqrs\Aggregate\DomainEventList;
use Accordia\Cqrs\Aggregate\AggregateRevision;

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
     * @param DomainEventList $eventLog
     * @param Metadata $metadata
     * @return CommitStreamInterface
     */
    public function appendEvents(DomainEventList $eventLog, Metadata $metadata): CommitStreamInterface;

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
     * @return CommitInterface
     */
    public function getHead(): CommitInterface;
}
