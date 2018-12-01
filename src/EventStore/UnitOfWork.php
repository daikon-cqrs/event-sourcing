<?php
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

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
use Daikon\EventSourcing\EventStore\Stream\StreamId;
use Daikon\EventSourcing\EventStore\Stream\StreamInterface;
use Daikon\EventSourcing\EventStore\Stream\StreamMap;
use Daikon\EventSourcing\EventStore\Stream\StreamProcessorInterface;
use Daikon\MessageBus\Metadata\Metadata;

final class UnitOfWork implements UnitOfWorkInterface
{
    private const MAX_RACE_ATTEMPTS = 5;

    /** @var string */
    private $aggregateRootType;

    /** @var StreamStorageInterface */
    private $streamStorage;

    /** @var ?StreamProcessorInterface */
    private $streamProcessor;

    /** @var string */
    private $streamImplementor;

    /** @var StreamMap */
    private $trackedCommitStreams;

    /** @var int */
    private $maxRaceAttempts;

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

    public function commit(AggregateRootInterface $aggregateRoot, Metadata $metadata): CommitSequenceInterface
    {
        $prevStream = $this->getTrackedStream($aggregateRoot);
        $updatedStream = $prevStream->appendEvents($aggregateRoot->getTrackedEvents(), $metadata);
        $result = $this->streamStorage->append($updatedStream, $prevStream->getStreamRevision());
        $raceCount = 0;
        while ($result instanceof StorageError) {
            if (++$raceCount > $this->maxRaceAttempts) {
                throw new ConcurrencyRaceLost($prevStream->getStreamId(), $aggregateRoot->getTrackedEvents());
            }
            $prevStream = $this->streamStorage->load($updatedStream->getStreamId());
            $conflictingEvents = $this->determineConflicts($aggregateRoot, $prevStream);
            if (!$conflictingEvents->isEmpty()) {
                throw new UnresolvableConflict($prevStream->getStreamId(), $conflictingEvents);
            }
            $updatedStream = $prevStream->appendEvents($aggregateRoot->getTrackedEvents(), $metadata);
            $result = $this->streamStorage->append($updatedStream, $prevStream->getStreamRevision());
        }
        $this->trackedCommitStreams = $this->trackedCommitStreams->unregister($prevStream->getStreamId());
        return $updatedStream->getCommitRange(
            $prevStream->getStreamRevision()->increment(),
            $updatedStream->getStreamRevision()
        );
    }

    public function checkout(AggregateIdInterface $aggregateId, AggregateRevision $revision): AggregateRootInterface
    {
        /** @var StreamId $streamId */
        $streamId = StreamId::fromNative($aggregateId->toNative());
        $stream = $this->streamStorage->load($streamId, $revision);
        if ($stream->isEmpty()) {
            throw new \Exception('Checking out empty streams is not supported.');
        }
        $aggregateRoot = call_user_func(
            [ $this->aggregateRootType, 'reconstituteFromHistory' ],
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
        $streamId = StreamId::fromNative((string)$aggregateRoot->getIdentifier());
        $tailRevision = $aggregateRoot->getTrackedEvents()->getTailRevision();
        if ($this->trackedCommitStreams->has((string)$streamId)) {
            $stream = $this->trackedCommitStreams->get((string)$streamId);
        } elseif ($tailRevision->isInitial()) {
            $stream = call_user_func([ $this->streamImplementor, 'fromStreamId' ], $streamId);
            $this->trackedCommitStreams = $this->trackedCommitStreams->register($stream);
        } else {
            throw new \Exception('AggregateRoot must be checked out before it may be committed.');
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
            if (!$targetRevision->isEmpty() && $commit->getAggregateRevision()->isGreaterThan($targetRevision)) {
                break;
            }
        }
        return $history;
    }

    private function determineConflicts(
        AggregateRootInterface $aggregateRoot,
        StreamInterface $stream
    ): DomainEventSequenceInterface {
        $conflictingEvents = DomainEventSequence::makeEmpty();
        $prevCommits = $stream->findCommitsSince($aggregateRoot->getRevision());
        /** @var CommitInterface $previousCommit */
        foreach ($prevCommits as $previousCommit) {
            /** @var DomainEventInterface $previousEvent */
            foreach ($previousCommit->getEventLog() as $previousEvent) {
                /** @var DomainEventInterface $newEvent */
                foreach ($aggregateRoot->getTrackedEvents() as $newEvent) {
                    if ($newEvent->conflictsWith($previousEvent)) {
                        $conflictingEvents = $conflictingEvents->push($newEvent);
                    }
                }
            }
        }
        return $conflictingEvents;
    }
}
