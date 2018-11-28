<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\EventStore\Commit;

use Daikon\EventSourcing\EventStore\Stream\StreamRevision;
use Ds\Vector;
use Daikon\MessageBus\MessageInterface;

final class CommitSequence implements CommitSequenceInterface
{
    /** @var Vector */
    private $compositeVector;

    public static function fromNative($commitsArray): CommitSequenceInterface
    {
        return new static(array_map(function (array $commitState): MessageInterface {
            return Commit::fromNative($commitState);
        }, $commitsArray));
    }

    public static function makeEmpty(): CommitSequenceInterface
    {
        return new self;
    }

    public function __construct(array $commits = [])
    {
        $this->compositeVector = (function (CommitInterface ...$commits): Vector {
            return new Vector($commits);
        })(...$commits);
    }

    public function push(CommitInterface $commit): CommitSequenceInterface
    {
        if (!$this->isEmpty()) {
            if (!$head = $this->getHead()) {
                throw new \RuntimeException("Corrupt sequence! Head not retrieveable for non-empty seq.");
            }
            $nextRevision = $head->getAggregateRevision()->increment();
            if (!$nextRevision->equals($commit->getEventLog()->getTailRevision())) {
                throw new \Exception(sprintf(
                    'Trying to add unexpected revision %s to event-sequence. Expected revision is %s',
                    $commit->getEventLog()->getTailRevision(),
                    $nextRevision
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

    public function getSlice(StreamRevision $start, StreamRevision $end): CommitSequenceInterface
    {
        return $this->compositeVector->reduce(function (
            CommitSequenceInterface $commits,
            CommitInterface $commit
        ) use (
            $start,
            $end
        ): CommitSequenceInterface {
            if ($commit->getStreamRevision()->isWithinRange($start, $end)) {
                /* @var CommitSequenceInterface $commits */
                $commits = $commits->push($commit);
                return $commits;
            }
            return $commits;
        }, new static);
    }

    public function isEmpty(): bool
    {
        return $this->compositeVector->isEmpty();
    }

    public function revisionOf(CommitInterface $commit): StreamRevision
    {
        $revision = $this->compositeVector->find($commit);
        if (is_bool($revision)) {
            return StreamRevision::makeInitial();
        }
        return StreamRevision::fromNative($revision);
    }

    public function getLength(): int
    {
        return $this->count();
    }

    public function count(): int
    {
        return $this->compositeVector->count();
    }

    public function getIterator(): \Traversable
    {
        return $this->compositeVector->getIterator();
    }

    private function __clone()
    {
        $this->compositeVector = clone $this->compositeVector;
    }
}
