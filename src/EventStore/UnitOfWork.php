<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\EventSourcing\EventStore;

use Daikon\EventSourcing\Aggregate\AggregateIdInterface;
use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Daikon\EventSourcing\Aggregate\AggregateRootInterface;
use Daikon\EventSourcing\Aggregate\Event\DomainEventInterface;
use Daikon\EventSourcing\Aggregate\Event\DomainEventSequence;
use Daikon\EventSourcing\Aggregate\Event\DomainEventSequenceInterface;
use Daikon\EventSourcing\EventStore\Commit\CommitInterface;
use Daikon\EventSourcing\EventStore\Commit\CommitSequenceInterface;
use Daikon\EventSourcing\EventStore\Storage\StorageError;
use Daikon\EventSourcing\EventStore\Storage\StreamStorageInterface;
use Daikon\EventSourcing\EventStore\Stream\Stream;
use Daikon\EventSourcing\EventStore\Stream\StreamInterface;
use Daikon\EventSourcing\EventStore\Stream\StreamMap;
use Daikon\EventSourcing\EventStore\Stream\StreamProcessorInterface;
use Daikon\Interop\RuntimeException;
use Daikon\Metadata\MetadataInterface;

final class UnitOfWork implements UnitOfWorkInterface
{
    private const MAX_RACE_ATTEMPTS = 3;

    private string $aggregateRootType;

    private StreamStorageInterface $streamStorage;

    private ?StreamProcessorInterface $streamProcessor;

    private string $streamImplementor;

    private StreamMap $trackedCommitStreams;

    private int $maxRaceAttempts;

    public function __construct(
        string $aggregateRootType,
        StreamStorageInterface $streamStorage,
        StreamProcessorInterface $streamProcessor = null,
        string $streamImplementor = Stream::class,
        int $maxRaceAttempts = self::MAX_RACE_ATTEMPTS
    ) {
        $this->aggregateRootType = $aggregateRootType;
        $this->streamStorage = $streamStorage;
        $this->streamProcessor = $streamProcessor;
        $this->streamImplementor = $streamImplementor;
        $this->trackedCommitStreams = StreamMap::makeEmpty();
        $this->maxRaceAttempts = $maxRaceAttempts;
    }

    public function commit(AggregateRootInterface $aggregateRoot, MetadataInterface $metadata): CommitSequenceInterface
    {
        $raceCount = 0;
        $previousStream = $this->getTrackedStream($aggregateRoot);
        $trackedEvents = $aggregateRoot->getTrackedEvents();
        $updatedStream = $previousStream->appendEvents($trackedEvents, $metadata);
        $result = $this->streamStorage->append($updatedStream, $previousStream->getHeadSequence());

        while ($result instanceof StorageError) {
            if (++$raceCount > $this->maxRaceAttempts) {
                throw new ConcurrencyRaceLost($previousStream->getAggregateId(), $aggregateRoot->getTrackedEvents());
            }
            $previousStream = $this->streamStorage->load($updatedStream->getAggregateId());
            $conflictingEvents = $this->determineConflicts($aggregateRoot, $previousStream);
            if (!$conflictingEvents->isEmpty()) {
                throw new UnresolvableConflict($previousStream->getAggregateId(), $conflictingEvents);
            }
            $resequencedEvents = $trackedEvents->resequence($previousStream->getHeadRevision());
            $updatedStream = $previousStream->appendEvents($resequencedEvents, $metadata);
            $result = $this->streamStorage->append($updatedStream, $previousStream->getHeadSequence());
        }

        $this->trackedCommitStreams = $this->trackedCommitStreams->unregister($previousStream->getAggregateId());

        return $updatedStream->getCommitRange(
            $previousStream->getHeadSequence()->increment(),
            $updatedStream->getHeadSequence()
        );
    }

    public function checkout(AggregateIdInterface $aggregateId, AggregateRevision $revision): AggregateRootInterface
    {
        $stream = $this->streamStorage->load($aggregateId, $revision);
        if ($stream->isEmpty()) {
            throw new RuntimeException('Checking out empty streams is not supported');
        }
        /** @var AggregateRootInterface $aggregateRoot */
        $aggregateRoot = call_user_func(
            [$this->aggregateRootType, 'reconstituteFromHistory'],
            $aggregateId,
            $this->prepareHistory(
                $this->streamProcessor ? $this->streamProcessor->process($stream) : $stream,
                $revision
            )
        );
        $this->trackedCommitStreams = $this->trackedCommitStreams->register($stream);
        return $aggregateRoot;
    }

    private function getTrackedStream(AggregateRootInterface $aggregateRoot): StreamInterface
    {
        $aggregateId = $aggregateRoot->getIdentifier();
        $tailRevision = $aggregateRoot->getTrackedEvents()->getTailRevision();
        if ($this->trackedCommitStreams->has((string)$aggregateId)) {
            /** @var StreamInterface $stream */
            $stream = $this->trackedCommitStreams->get((string)$aggregateId);
        } elseif ($tailRevision->isInitial()) {
            $stream = call_user_func([$this->streamImplementor, 'fromAggregateId'], $aggregateId);
            $this->trackedCommitStreams = $this->trackedCommitStreams->register($stream);
        } else {
            throw new RuntimeException('AggregateRoot must be checked out before it may be committed');
        }
        return $stream;
    }

    private function prepareHistory(
        StreamInterface $stream,
        AggregateRevision $targetRevision
    ): DomainEventSequenceInterface {
        $history = DomainEventSequence::makeEmpty();
        /** @var CommitInterface $commit */
        foreach ($stream as $commit) {
            $history = $history->append($commit->getEventLog());
        }
        if (!$targetRevision->isEmpty() && !$history->getHeadRevision()->equals($targetRevision)) {
            throw new RuntimeException(sprintf(
                'AggregateRoot cannot be reconstituted to revision %s',
                (string)$targetRevision
            ));
        }
        return $history;
    }

    private function determineConflicts(
        AggregateRootInterface $aggregateRoot,
        StreamInterface $stream
    ): DomainEventSequenceInterface {
        $conflictingEvents = DomainEventSequence::makeEmpty();
        $tailRevision = $aggregateRoot->getTrackedEvents()->getTailRevision();
        $previousCommits = $stream->findCommitsSince($tailRevision);
        /** @var CommitInterface $previousCommit */
        foreach ($previousCommits as $previousCommit) {
            /** @var DomainEventInterface $previousEvent */
            foreach ($previousCommit->getEventLog() as $previousEvent) {
                /** @var DomainEventInterface $previousEvent */
                foreach ($aggregateRoot->getTrackedEvents() as $trackedEvent) {
                    //All events from the first conflict onwards are considered to be in conflict
                    if (!$conflictingEvents->isEmpty() || $trackedEvent->conflictsWith($previousEvent)) {
                        $conflictingEvents = $conflictingEvents->push($previousEvent);
                        break;
                    }
                }
            }
        }
        return $conflictingEvents;
    }
}
