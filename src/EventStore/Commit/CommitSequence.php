<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\EventSourcing\EventStore\Commit;

use Daikon\EventSourcing\EventStore\Stream\Sequence;
use Ds\Vector;
use RuntimeException;
use Traversable;

final class CommitSequence implements CommitSequenceInterface
{
    /** @var Vector */
    private $compositeVector;

    /** @param array $commits */
    public static function fromNative($commits): CommitSequenceInterface
    {
        return new self(array_map(function (array $state): CommitInterface {
            return Commit::fromNative($state);
        }, $commits));
    }

    public static function makeEmpty(): CommitSequenceInterface
    {
        return new self;
    }

    public function __construct(iterable $commits = [])
    {
        $this->compositeVector = (function (CommitInterface ...$commits): Vector {
            return new Vector($commits);
        })(...$commits);
    }

    public function push(CommitInterface $commit): CommitSequenceInterface
    {
        if (!$this->isEmpty()) {
            $nextRevision = $this->getHead()->getHeadRevision()->increment();
            if (!$nextRevision->equals($commit->getTailRevision())) {
                throw new RuntimeException(sprintf(
                    'Trying to add unexpected revision %s to event-sequence. Expected revision is %s',
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
        return array_map(function (CommitInterface $commit): array {
            $nativeRep = $commit->toNative();
            $nativeRep['@type'] = get_class($commit);
            return $nativeRep;
        }, $this->compositeVector->toArray());
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
        return $this->compositeVector->offsetExists($offset);
    }

    public function get(Sequence $sequence): CommitInterface
    {
        $offset = $sequence->toNative() - 1;
        return $this->compositeVector->get($offset);
    }

    public function getSlice(Sequence $start, Sequence $end): CommitSequenceInterface
    {
        return $this->compositeVector->reduce(function (
            CommitSequenceInterface $commits,
            CommitInterface $commit
        ) use (
            $start,
            $end
        ): CommitSequenceInterface {
            if ($commit->getSequence()->isWithinRange($start, $end)) {
                $commits = $commits->push($commit);
                return $commits;
            }
            return $commits;
        }, new self);
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

    public function getLength(): int
    {
        return $this->count();
    }

    public function count(): int
    {
        return $this->compositeVector->count();
    }

    public function getIterator(): Traversable
    {
        return $this->compositeVector->getIterator();
    }

    private function __clone()
    {
        $this->compositeVector = clone $this->compositeVector;
    }
}
