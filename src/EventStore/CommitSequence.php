<?php

namespace Daikon\Cqrs\EventStore;

use Countable;
use Ds\Vector;
use Iterator;
use IteratorAggregate;

final class CommitSequence implements IteratorAggregate, Countable
{
    /**
     * @var Vector
     */
    private $compositeVector;

    /**
     * @param array $commitsArray
     * @return CommitSequence
     */
    public static function fromArray(array $commitsArray): CommitSequence
    {
        return new static(array_map(function (array $commitState) {
            return Commit::fromArray($commitState);
        }, $commitsArray));
    }

    /**
     * @return CommitSequence
     */
    public static function makeEmpty(): CommitSequence
    {
        return new self;
    }

    /**
     * @param CommitInterface[] $commits
     */
    public function __construct(array $commits = [])
    {
        (function (CommitInterface ...$commits) {
            $this->compositeVector = new Vector($commits);
        })(...$commits);
    }

    /**
     * @param  CommitInterface $commit
     * @return CommitSequence
     */
    public function push(CommitInterface $commit): self
    {
        if (!$this->isEmpty() && !$this->getHeadRevision()->increment()->equals($commit->getAggregateRevision())) {
            throw new \Exception(sprintf(
                "Trying to add unexpected revision %s to event-sequence. Expected revision is $nextRevision",
                $commit->getAggregateRevision()
            ));
        }
        $commitSequence = clone $this;
        $commitSequence->compositeVector->push($commit);
        return $commitSequence;
    }

    /**
     * @return mixed[]
     */
    public function toNative(): array
    {
        return array_map(function (CommitInterface $commit) {
            $nativeRep = $commit->toArray();
            $nativeRep["@type"] = get_class($commit);
            return $nativeRep;
        });
    }

    /**
     * @return CommitInterface|null
     */
    public function getTail(): ?CommitInterface
    {
        return $this->isEmpty() ? null : $this->compositeVector->first();
    }

    /**
     * @return CommitInterface|null
     */
    public function getHead(): ?CommitInterface
    {
        return $this->isEmpty() ? null : $this->compositeVector->last();
    }

    /**
     * @return CommitInterface
     */
    public function get(CommitStreamRevision $streamRevision): CommitInterface
    {
        return $this->compositeVector->get($streamRevision->toNative());
    }

    /**
     * @param CommitStreamRevision $start
     * @param CommitStreamRevision $end
     * @return CommitSequence
     */
    public function getSlice(CommitStreamRevision $start, CommitStreamRevision $end): self
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

    /**
     * @return boolean
     */
    public function isEmpty(): bool
    {
        return $this->compositeVector->isEmpty();
    }

    /**
     * @param CommitInterface $commit
     * @return CommitStreamRevision
     */
    public function revisionOf(CommitInterface $commit): CommitStreamRevision
    {
        return StreamRevision::fromNative($this->compositeVector->find($commit));
    }

    /**
     * @return int
     */
    public function getLength(): int
    {
        return $this->count();
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->compositeVector->count();
    }

    /**
     * @return Iterator
     */
    public function getIterator(): Iterator
    {
        return $this->compositeVector->getIterator();
    }

    private function __clone()
    {
        $this->compositeVector = clone $this->compositeVector;
    }
}
