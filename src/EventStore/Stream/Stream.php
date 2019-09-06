<?php

/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\EventStore\Stream;

use Assert\Assertion;
use Daikon\EventSourcing\Aggregate\AggregateId;
use Daikon\EventSourcing\Aggregate\AggregateIdInterface;
use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Daikon\EventSourcing\Aggregate\Event\DomainEventSequenceInterface;
use Daikon\EventSourcing\EventStore\Commit\Commit;
use Daikon\EventSourcing\EventStore\Commit\CommitInterface;
use Daikon\EventSourcing\EventStore\Commit\CommitSequence;
use Daikon\EventSourcing\EventStore\Commit\CommitSequenceInterface;
use Daikon\Metadata\MetadataInterface;

final class Stream implements StreamInterface
{
    /** @var AggregateIdInterface */
    private $aggregateId;

    /** @var CommitSequenceInterface */
    private $commitSequence;

    /** @var string */
    private $commitImplementor;

    public static function fromAggregateId(
        AggregateIdInterface $aggregateId,
        string $commitImplementor = Commit::class
    ): StreamInterface {
        return new self($aggregateId);
    }

    /** @param array $state */
    public static function fromNative($state): Stream
    {
        Assertion::keyExists($state, 'aggregateId');
        Assertion::keyExists($state, 'commitSequence');

        return new self(
            AggregateId::fromNative($state['aggregateId']),
            CommitSequence::fromNative($state['commitSequence']),
            $state['commitImplementor'] ?? null
        );
    }

    public function getAggregateId(): AggregateIdInterface
    {
        return $this->aggregateId;
    }

    public function getSequence(): Sequence
    {
        return Sequence::fromNative($this->commitSequence->getLength());
    }

    public function getAggregateRevision(): AggregateRevision
    {
        $head = $this->commitSequence->getHead();
        return $head ? $head->getAggregateRevision() : AggregateRevision::makeEmpty();
    }

    public function appendEvents(DomainEventSequenceInterface $eventLog, MetadataInterface $metadata): StreamInterface
    {
        return $this->appendCommit(
            call_user_func(
                [$this->commitImplementor, 'make'],
                $this->aggregateId,
                $this->getSequence()->increment(),
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

    public function getCommitRange(Sequence $fromRev, Sequence $toRev = null): CommitSequenceInterface
    {
        return $this->commitSequence->getSlice($fromRev, $toRev ?? $this->getSequence());
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
            'aggregateId' => $this->aggregateId->toNative(),
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
            $prevCommit = $this->commitSequence->get($prevCommit->getSequence()->decrement());
        }
        return new CommitSequence(array_reverse($previousCommits));
    }

    private function __construct(
        AggregateIdInterface $aggregateId,
        CommitSequenceInterface $commitSequence = null,
        string $commitImplementor = null
    ) {
        $this->aggregateId = $aggregateId;
        $this->commitSequence = $commitSequence ?? new CommitSequence;
        $this->commitImplementor = $commitImplementor ?? Commit::class;
    }
}
