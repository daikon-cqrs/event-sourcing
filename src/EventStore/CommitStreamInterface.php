<?php

namespace Accordia\Cqrs\EventStore;

use Accordia\MessageBus\Metadata\Metadata;
use Accordia\Cqrs\Aggregate\DomainEventList;
use Accordia\Cqrs\Aggregate\Revision;

interface CommitStreamInterface extends \IteratorAggregate, \Countable
{
    /**
     * @param StreamId $streamId
     * @param string $commitImplementor
     * @return CommitStreamInterface
     */
    public static function fromStreamId(
        StreamId $streamId,
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
     * @param Revision $fromRev
     * @param Revision|null $toRev
     * @return CommitSequence
     */
    public function getCommitRange(Revision $fromRev, Revision $toRev = null): CommitSequence;

    /**
     * @return StreamId
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
     * @return CommitInterface
     */
    public function getHead(): CommitInterface;
}
