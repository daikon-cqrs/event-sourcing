<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\EventSourcing\EventStore\Commit;

use Daikon\EventSourcing\EventStore\Stream\Sequence;
use Daikon\Interop\RuntimeException;
use Ds\Vector;

final class CommitSequence implements CommitSequenceInterface
{
    private Vector $compositeVector;

    /** @param array $commits */
    public static function fromNative($commits): self
    {
        return new self(array_map(
            fn(array $state): CommitInterface => Commit::fromNative($state),
            $commits
        ));
    }

    public static function makeEmpty(): self
    {
        return new self;
    }

    public function __construct(iterable $commits = [])
    {
        $this->compositeVector = (
            fn(CommitInterface ...$commits): Vector => new Vector($commits)
        )(...$commits);
    }

    public function push(CommitInterface $commit): self
    {
        if (!$this->isEmpty()) {
            $nextRevision = $this->getHead()->getHeadRevision()->increment();
            if (!$nextRevision->equals($commit->getTailRevision())) {
                throw new RuntimeException(sprintf(
                    'Trying to add invalid revision %s to event-sequence, expected revision is %s',
                    (string)$commit->getHeadRevision(),
                    (string)$nextRevision
                ));
            }
        }
        $commitSequence = clone $this;
        $commitSequence->compositeVector->push($commit);
        return $commitSequence;
    }

    public function toNative(): array
    {
        $nativeList = [];
        foreach ($this as $commit) {
            $nativeRep = $commit->toNative();
            $nativeRep['@type'] = get_class($commit);
            $nativeList[] = $nativeRep;
        }
        return $nativeList;
    }

    public function getTail(): CommitInterface
    {
        return $this->compositeVector->first();
    }

    public function getHead(): CommitInterface
    {
        return $this->compositeVector->last();
    }

    public function has(Sequence $sequence): bool
    {
        $offset = $sequence->toNative() - 1;
        /** @psalm-suppress UndefinedMethod */
        return isset($this->compositeVector[$offset]);
    }

    public function get(Sequence $sequence): CommitInterface
    {
        $offset = $sequence->toNative() - 1;
        return $this->compositeVector->get($offset);
    }

    public function getSlice(Sequence $start, Sequence $end): self
    {
        /** @psalm-suppress InvalidArgument */
        return $this->compositeVector->reduce(
            function (CommitSequenceInterface $commits, CommitInterface $commit) use ($start, $end): self {
                if ($commit->getSequence()->isWithinRange($start, $end)) {
                    $commits = $commits->push($commit);
                }
                return $commits;
            },
            new self
        );
    }

    public function isEmpty(): bool
    {
        return $this->compositeVector->isEmpty();
    }

    public function revisionOf(CommitInterface $commit): Sequence
    {
        $revision = $this->compositeVector->find($commit);
        if (is_bool($revision)) {
            return Sequence::makeInitial();
        }
        return Sequence::fromNative($revision);
    }

    public function count(): int
    {
        return $this->compositeVector->count();
    }

    public function getIterator(): Vector
    {
        return $this->compositeVector;
    }

    private function __clone()
    {
        $this->compositeVector = clone $this->compositeVector;
    }
}
