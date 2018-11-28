<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\EventStore\Stream;

use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Daikon\EventSourcing\Aggregate\Event\DomainEventSequenceInterface;
use Daikon\EventSourcing\EventStore\Commit\Commit;
use Daikon\EventSourcing\EventStore\Commit\CommitInterface;
use Daikon\EventSourcing\EventStore\Commit\CommitSequence;
use Daikon\EventSourcing\EventStore\Commit\CommitSequenceInterface;
use Daikon\MessageBus\Metadata\Metadata;

final class Stream implements StreamInterface
{
    /** @var StreamIdInterface */
    private $streamId;

    /** @var CommitSequenceInterface */
    private $commitSequence;

    /** @var string */
    private $commitImplementor;

    public static function fromStreamId(
        StreamIdInterface $streamId,
        string $commitImplementor = Commit::class
    ): StreamInterface {
        return new static($streamId);
    }

    public static function fromNative($streamState): Stream
    {
        return new static(
            StreamId::fromNative($streamState['commitStreamId']),
            CommitSequence::fromNative($streamState['commitStreamSequence']),
            $streamState['commitImplementor']
        );
    }

    public function __construct(
        StreamIdInterface $streamId,
        CommitSequenceInterface $commitSequence = null,
        string $commitImplementor = Commit::class
    ) {
        $this->streamId = $streamId;
        $this->commitSequence = $commitSequence ?? new CommitSequence;
        $this->commitImplementor = $commitImplementor;
    }

    public function getStreamId(): StreamIdInterface
    {
        return $this->streamId;
    }

    public function getStreamRevision(): StreamRevision
    {
        return StreamRevision::fromNative($this->commitSequence->getLength());
    }

    public function getAggregateRevision(): AggregateRevision
    {
        $head = $this->commitSequence->getHead();
        return $head ? $head->getAggregateRevision() : AggregateRevision::makeEmpty();
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

    public function getCommitRange(StreamRevision $fromRev, StreamRevision $toRev = null): CommitSequenceInterface
    {
        return $this->commitSequence->getSlice($fromRev, $toRev ?? $this->getStreamRevision());
    }

    public function count(): int
    {
        return $this->commitSequence->count();
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function toNative(): array
    {
        return [
            'commitSequence' => $this->commitSequence->toNative(),
            'streamId' => $this->streamId->toNative(),
            'commitImplementor' => $this->commitImplementor
        ];
    }

    public function getIterator(): \Traversable
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
