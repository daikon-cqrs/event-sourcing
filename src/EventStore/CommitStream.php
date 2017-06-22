<?php

namespace Accordia\Cqrs\EventStore;

use Accordia\Cqrs\Aggregate\AggregateRevision;
use Accordia\Cqrs\Aggregate\DomainEventSequence;
use Accordia\MessageBus\Metadata\Metadata;

final class CommitStream implements CommitStreamInterface
{
    /**
     * @var CommitStreamId
     */
    private $streamId;

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
     * @param mixed[] $streamState
     * @return CommitStream
     */
    public static function fromArray(array $streamState): CommitStream
    {
        return new static(
            CommitStreamId::fromNative($streamState["commitStreamId"]),
            CommitSequence::fromArray($streamState["commitStreamSequence"]),
            $commitStreamState["commitImplementor"]
        );
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
        return CommitStreamRevision::fromNative($this->commitSequence->getLength());
    }

    /**
     * @return AggregateRevision
     */
    public function getAggregateRevision(): AggregateRevision
    {
        return $this->commitSequence->getHead()->getAggregateRevision();
    }

    /**
     * @param DomainEventSequence $eventLog
     * @param Metadata $metadata
     * @return CommitStreamInterface
     */
    public function appendEvents(DomainEventSequence $eventLog, Metadata $metadata): CommitStreamInterface
    {
        $previousCommits = $this->findCommitsSince($eventLog->getHeadRevision());
        if (!$previousCommits->isEmpty()) {
            $conflictingEvents = $this->detectConflictingEvents($eventLog, $conflictingCommits);
            // @todo pass $conflictingEvents to an exception and throw?
        }
        return $this->appendCommit(
            $this->commitImplementor::make(
                $this->streamId,
                $this->getStreamRevision()->increment(),
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
        $stream = clone $this;
        $stream->commitSequence = $this->commitSequence->push($commit);
        return $stream;
    }

    /**
     * @return CommitInterface|null
     */
    public function getHead(): ?CommitInterface
    {
        return $this->commitSequence->isEmpty() ? null : $this->commitSequence->getHead();
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
     * @return mixed[]
     */
    public function toNative(): array
    {
        return [
            "commitSequence" => $this->commitSequence->toNative(),
            "streamId" => $this->streamId->toNative(),
            "commitImplementor" => $this->commitImplementor
        ];
    }

    /**
     * @return Iterator
     */
    public function getIterator(): \Iterator
    {
        return $this->commitSequence->getIterator();
    }

    /**
     * @param AggregateRevision $incomingRevision
     * @return CommitSequence
     */
    private function findCommitsSince(AggregateRevision $incomingRevision): CommitSequence
    {
        $previousCommits = [];
        $prevCommit = $this->getHead();
        while ($prevCommit && $incomingRevision->isLessThanOrEqual($prevCommit->getAggregateRevision())) {
            $previousCommits[] = $prevCommit;
            $prevCommit = $this->commitSequence->get($prevCommit->getStreamRevision()->decrement());
        }
        return new CommitSequence(array_reverse($previousCommits));
    }

    /**
     * @param DomainEventSequence $newEvents
     * @param CommitSequence $conflictingCommits
     * @return DomainEventInterface[]
     */
    private function detectConflictingEvents(DomainEventSequence $newEvents, CommitSequence $previousCommits): array
    {
        $conflictingEvents = [];
        foreach ($newEvents as $newEvent) {
            foreach ($previousCommits as $previousCommit) {
                foreach ($previousCommit->getEventLog() as $previousEvent) {
                    /* @todo figure out how to do conflict resolution.
                    if ($newEvent->conflictsWith($previousEvent)) {
                        $conflictingEvents[] = [ $previousEvent, $newEvent ];
                    }
                    */
                }
            }
        }
        return $conflictingEvents;
    }
}
