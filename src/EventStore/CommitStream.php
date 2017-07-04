<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Cqrs\EventStore;

use Daikon\Cqrs\Aggregate\AggregateRevision;
use Daikon\Cqrs\Aggregate\DomainEventSequence;
use Daikon\MessageBus\Metadata\Metadata;

final class CommitStream implements CommitStreamInterface
{
    private $streamId;

    private $commitSequence;

    private $commitImplementor;

    public static function fromStreamId(
        CommitStreamId $streamId,
        string $commitImplementor = Commit::class
    ): CommitStreamInterface {
        return new static($streamId);
    }

    public static function fromArray(array $streamState): CommitStream
    {
        return new static(
            CommitStreamId::fromNative($streamState["commitStreamId"]),
            CommitSequence::fromArray($streamState["commitStreamSequence"]),
            $streamState["commitImplementor"]
        );
    }

    public function __construct(
        CommitStreamId $streamId,
        CommitSequence $commitSequence = null,
        string $commitImplementor = Commit::class
    ) {
        $this->streamId = $streamId;
        $this->commitSequence = $commitSequence ?? new CommitSequence;
        $this->commitImplementor = $commitImplementor;
    }

    public function getStreamId(): CommitStreamId
    {
        return $this->streamId;
    }

    public function getStreamRevision(): CommitStreamRevision
    {
        return CommitStreamRevision::fromNative($this->commitSequence->getLength());
    }

    public function getAggregateRevision(): AggregateRevision
    {
        return $this->commitSequence->getHead()->getAggregateRevision();
    }

    public function appendEvents(DomainEventSequence $eventLog, Metadata $metadata): CommitStreamInterface
    {
        $previousCommits = $this->findCommitsSince($eventLog->getHeadRevision());
        if (!$previousCommits->isEmpty()) {
            $conflictingEvents = $this->detectConflictingEvents($eventLog, $previousCommits);
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

    public function appendCommit(CommitInterface $commit): CommitStreamInterface
    {
        $stream = clone $this;
        $stream->commitSequence = $this->commitSequence->push($commit);
        return $stream;
    }

    public function getHead(): ?CommitInterface
    {
        return $this->commitSequence->isEmpty() ? null : $this->commitSequence->getHead();
    }

    public function getCommitRange(CommitStreamRevision $fromRev, CommitStreamRevision $toRev = null): CommitSequence
    {
        return $this->commitSequence->getSlice($fromRev, $toRev ?? $this->getStreamRevision());
    }

    public function count(): int
    {
        return $this->commitSequence->count();
    }

    public function toNative(): array
    {
        return [
            "commitSequence" => $this->commitSequence->toNative(),
            "streamId" => $this->streamId->toNative(),
            "commitImplementor" => $this->commitImplementor
        ];
    }

    public function getIterator(): \Iterator
    {
        return $this->commitSequence->getIterator();
    }

    private function findCommitsSince(AggregateRevision $incomingRevision): CommitSequence
    {
        $previousCommits = [];
        $prevCommit = $this->getHead();
        while ($prevCommit && $incomingRevision->isLessThan($prevCommit->getAggregateRevision())) {
            $previousCommits[] = $prevCommit;
            $prevCommit = $this->commitSequence->get($prevCommit->getStreamRevision()->decrement());
        }
        return new CommitSequence(array_reverse($previousCommits));
    }

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
