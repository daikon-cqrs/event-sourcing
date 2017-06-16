<?php

namespace Accordia\Cqrs\EventStore;

use Accordia\Cqrs\Aggregate\Revision;
use Accordia\DataStructures\TypedListTrait;

class CommitSequence implements \IteratorAggregate, \Countable
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
     * @param Revision $start
     * @param Revision $end
     * @return CommitSequence
     */
    public function getSlice(Revision $start, Revision $end): self
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
