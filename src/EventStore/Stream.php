<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\EventStore;

use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Daikon\EventSourcing\Aggregate\DomainEventSequence;
use Daikon\MessageBus\Metadata\Metadata;

final class Stream implements StreamInterface
{
    /** @var StreamId */
    private $streamId;

    /** @var CommitSequence */
    private $commitSequence;

    /** @var string */
    private $commitImplementor;

    public static function fromStreamId(StreamId $streamId, string $commitImplementor = Commit::class): StreamInterface
    {
        return new static($streamId);
    }

    public static function fromArray(array $streamState): Stream
    {
        return new static(
            StreamId::fromNative($streamState["commitStreamId"]),
            CommitSequence::fromArray($streamState["commitStreamSequence"]),
            $streamState["commitImplementor"]
        );
    }

    public function __construct(
        StreamId $streamId,
        CommitSequence $commitSequence = null,
        string $commitImplementor = Commit::class
    ) {
        $this->streamId = $streamId;
        $this->commitSequence = $commitSequence ?? new CommitSequence;
        $this->commitImplementor = $commitImplementor;
    }

    public function getStreamId(): StreamId
    {
        return $this->streamId;
    }

    public function getStreamRevision(): StreamRevision
    {
        return StreamRevision::fromNative($this->commitSequence->getLength());
    }

    public function getAggregateRevision(): AggregateRevision
    {
        return $this->commitSequence->getHead()->getAggregateRevision();
    }

    public function appendEvents(DomainEventSequence $eventLog, Metadata $metadata): StreamInterface
    {
        $previousCommits = $this->findCommitsSince($eventLog->getHeadRevision());
        if (!$previousCommits->isEmpty()) {
            $conflictingEvents = $this->detectConflictingEvents($eventLog, $previousCommits);
            // @todo We need to indicate to the caller, that appending did not work and provide the conflicting events.
            // Here are some possible solutions:
            // 1. Throw an special exception, that contains the conflicting events
            //    Con: This is not nice, because it would be misusing exceptions for control-flow
            // 2. Add a two new methods isConflicted, getConflicting eventsto the StreamInterface.
            //    Have this method return a new stream that is marked as conflicted and yields the conflicting events.
            //    Con: Now you need check your stream for the conflicted state before further processing them.
            // 3. Introduce a StreamResultInterface with two implementations for Success/Error.
            //    Success would hold the new stream with appended events and Error would yield the conflict infos.
            //    Con: More result interfaces/classes.
            // 4. Same as 3. but use the Ok/Error monads from shrink0r/monatic.
            //    These would then also replace the StoreResultInterface to preserve consistency.
            //    Con: As this approach is more generic we lose explicity, no?
        }
        return $this->appendCommit(
            call_user_func(
                [ $this->commitImplementor, 'make' ],
                $this->streamId,
                $this->getStreamRevision()->increment(),
                $eventLog,
                $metadata
            )
        );
    }

    public function appendCommit(CommitInterface $commit): StreamInterface
    {
        $stream = clone $this;
        $stream->commitSequence = $this->commitSequence->push($commit);
        return $stream;
    }

    public function getHead(): ?CommitInterface
    {
        return $this->commitSequence->isEmpty() ? null : $this->commitSequence->getHead();
    }

    public function getCommitRange(StreamRevision $fromRev, StreamRevision $toRev = null): CommitSequence
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
                    if ($newEvent->conflictsWith($previousEvent)) {
                        $conflictingEvents[] = [ $previousEvent, $newEvent ];
                    }
                }
            }
        }
        return $conflictingEvents;
    }
}
