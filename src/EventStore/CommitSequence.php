<?php

namespace Accordia\Cqrs\EventStore;

use Accordia\DataStructures\TypedListTrait;

final class CommitSequence implements \IteratorAggregate, \Countable
{
    use TypedListTrait;

    /**
     * @param array $commits
     */
    public function __construct(array $commits = [])
    {
        $this->init($commits, CommitInterface::class);
    }

    /**
     * @return CommitInterface
     */
    public function getHead(): CommitInterface
    {
        return $this->compositeVector->last();
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
}
