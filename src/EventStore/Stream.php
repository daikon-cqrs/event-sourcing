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
use Daikon\EventSourcing\Aggregate\DomainEventSequenceInterface;
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
            StreamId::fromNative($streamState['commitStreamId']),
            CommitSequence::fromArray($streamState['commitStreamSequence']),
            $streamState['commitImplementor']
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

    public function appendEvents(DomainEventSequenceInterface $eventLog, Metadata $metadata): StreamInterface
    {
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
            'commitSequence' => $this->commitSequence->toNative(),
            'streamId' => $this->streamId->toNative(),
            'commitImplementor' => $this->commitImplementor
        ];
    }

    public function getIterator(): \Iterator
    {
        return $this->commitSequence->getIterator();
    }

    public function findCommitsSince(AggregateRevision $incomingRevision): CommitSequenceInterface
    {
        $previousCommits = [];
        $prevCommit = $this->getHead();
        while ($prevCommit && $incomingRevision->isLessThan($prevCommit->getAggregateRevision())) {
            $previousCommits[] = $prevCommit;
            $prevCommit = $this->commitSequence->get($prevCommit->getStreamRevision()->decrement());
        }
        return new CommitSequence(array_reverse($previousCommits));
    }
}
