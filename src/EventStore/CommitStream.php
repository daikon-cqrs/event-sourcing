<?php

namespace Accordia\Cqrs\EventStore;

use Accordia\MessageBus\Metadata\Metadata;
use Accordia\Cqrs\Aggregate\DomainEventList;
use Accordia\Cqrs\Aggregate\AggregateRevision;

final class CommitStream implements CommitStreamInterface
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
     * @var CommitSequence
     */
    private $commitSequence;

    /**
     * @var string
     */
    private $commitImplementor;

    /**
     * @param CommitStreamId $streamId
     * @param string $commitImplementor
     * @return CommitStreamInterface
     */
    public static function fromStreamId(
        CommitStreamId $streamId,
        string $commitImplementor = Commit::class
    ): CommitStreamInterface {
        return new static($streamId);
    }

    /**
     * @param CommitStreamId $streamId
     * @param CommitSequence|null $commitSequence
     * @param string $commitImplementor
     */
    public function __construct(
        CommitStreamId $streamId,
        CommitSequence $commitSequence = null,
        string $commitImplementor = Commit::class
    ) {
        $this->streamId = $streamId;
        $this->commitSequence = $commitSequence ?? new CommitSequence;
        $this->commitImplementor = $commitImplementor;
        $this->streamRevision = CommitStreamRevision::fromNative($this->commitSequence->count());
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
        return $this->commitSequence->getHead()->getAggregateRevision();
    }

    /**
     * @param DomainEventList $eventLog
     * @param Metadata $metadata
     * @return CommitStreamInterface
     */
    public function appendEvents(DomainEventList $eventLog, Metadata $metadata): CommitStreamInterface
    {
        return $this->appendCommit(
            $this->commitImplementor::make(
                $this->streamId,
                $this->streamRevision->increment(),
                $eventLog,
                $metadata
            )
        );
    }

    /**
     * @param CommitInterface $commit
     * @return CommitStreamInterface
     * @throws \Exception
     */
    public function appendCommit(CommitInterface $commit): CommitStreamInterface
    {
        if (!$commit->getStreamRevision()->equals($this->streamRevision->increment())) {
            throw new \Exception(sprintf(
                "Trying to commit revision %s with current HEAD being %s. Expected revision %s",
                $commit->getRevision(),
                $this->streamRevision->getRevision(),
                $this->streamRevision->getRevision()->increment()
            ));
        }
        $copy = clone $this;
        $copy->commitSequence = $this->commitSequence->push($commit);
        $copy->streamRevision = $this->streamRevision->increment();
        return $copy;
    }

    /**
     * @return CommitInterface
     */
    public function getHead(): CommitInterface
    {
        return $this->commitSequence->getHead();
    }

    /**
     * @param CommitStreamRevision $fromRev
     * @param CommitStreamRevision|null $toRev
     * @return CommitSequence
     */
    public function getCommitRange(CommitStreamRevision $fromRev, CommitStreamRevision $toRev = null): CommitSequence
    {
        return $this->commitSequence->getSlice($fromRev, $toRev ?? $this->getStreamRevision());
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->commitSequence->count();
    }

    /**
     * @return \Iterator
     */
    public function getIterator(): \Iterator
    {
        return $this->commitSequence->getIterator();
    }
}
