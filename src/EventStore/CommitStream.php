<?php

namespace Accordia\Cqrs\EventStore;

use Accordia\MessageBus\Metadata\Metadata;
use Accordia\Cqrs\Aggregate\DomainEventList;
use Accordia\Cqrs\Aggregate\Revision;

class CommitStream implements CommitStreamInterface
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
     * @var CommitSequence
     */
    private $commitSequence;

    /**
     * @var string
     */
    private $commitImplementor;

    /**
     * @param StreamId $streamId
     * @param string $commitImplementor
     * @return CommitStreamInterface
     */
    public static function fromStreamId(
        StreamId $streamId,
        string $commitImplementor = Commit::class
    ): CommitStreamInterface {
        return new static($streamId);
    }

    /**
     * @param StreamId $streamId
     * @param CommitSequence|null $commitSequence
     * @param string $commitImplementor
     */
    public function __construct(
        StreamId $streamId,
        CommitSequence $commitSequence = null,
        string $commitImplementor = Commit::class
    ) {
        $this->streamId = $streamId;
        $this->commitSequence = $commitSequence ?? new CommitSequence;
        $this->commitImplementor = $commitImplementor;
        $this->streamRevision = Revision::fromNative($this->commitSequence->count());
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
     * @param Revision $fromRev
     * @param Revision|null $toRev
     * @return CommitSequence
     */
    public function getCommitRange(Revision $fromRev, Revision $toRev = null): CommitSequence
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
