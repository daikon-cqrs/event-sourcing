<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\EventStore;

use Countable;
use Ds\Vector;
use Iterator;
use IteratorAggregate;

final class CommitSequence implements IteratorAggregate, Countable
{
    /** @var Vector */
    private $compositeVector;

    public static function fromArray(array $commitsArray): CommitSequence
    {
        return new static(array_map(function (array $commitState) {
            return Commit::fromArray($commitState);
        }, $commitsArray));
    }

    public static function makeEmpty(): CommitSequence
    {
        return new self;
    }

    public function __construct(array $commits = [])
    {
        $this->compositeVector = (function (CommitInterface ...$commits): Vector {
            return new Vector($commits);
        })(...$commits);
    }

    public function push(CommitInterface $commit): self
    {
        if (!$this->isEmpty()) {
            $nextRevision = $this->getHead()->getAggregateRevision()->increment();
            if (!$nextRevision->equals($commit->getAggregateRevision())) {
                throw new \Exception(sprintf(
                    "Trying to add unexpected revision %s to event-sequence. Expected revision is $nextRevision",
                    $commit->getAggregateRevision()
                ));
            }
        }
        $commitSequence = clone $this;
        $commitSequence->compositeVector->push($commit);
        return $commitSequence;
    }

    public function toNative(): array
    {
        return array_map(function (CommitInterface $commit): array {
            $nativeRep = $commit->toArray();
            $nativeRep["@type"] = get_class($commit);
            return $nativeRep;
        }, $this->compositeVector->toArray());
    }

    public function getTail(): ?CommitInterface
    {
        return $this->isEmpty() ? null : $this->compositeVector->first();
    }

    public function getHead(): ?CommitInterface
    {
        return $this->isEmpty() ? null : $this->compositeVector->last();
    }

    public function get(StreamRevision $streamRevision): ?CommitInterface
    {
        if ($this->compositeVector->offsetExists($streamRevision->toNative() - 1)) {
            $this->compositeVector->get($streamRevision->toNative() - 1);
        }
        return null;
    }

    public function getSlice(StreamRevision $start, StreamRevision $end): self
    {
        return $this->compositeVector->reduce(
            function (CommitSequence $commits, CommitInterface $commit) use ($start, $end): CommitSequence {
                if ($commit->getStreamRevision()->isWithinRange($start, $end)) {
                    $commits = $commits->push($commit); /* @var CommitSequence $commits */
                    return $commits;
                }
                return $commits;
            },
            new static
        );
    }

    public function isEmpty(): bool
    {
        return $this->compositeVector->isEmpty();
    }

    public function revisionOf(CommitInterface $commit): StreamRevision
    {
        return StreamRevision::fromNative($this->compositeVector->find($commit));
    }

    public function getLength(): int
    {
        return $this->count();
    }

    public function count(): int
    {
        return $this->compositeVector->count();
    }

    public function getIterator(): Iterator
    {
        return $this->compositeVector->getIterator();
    }

    private function __clone()
    {
        $this->compositeVector = clone $this->compositeVector;
    }
}
