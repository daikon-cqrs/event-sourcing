<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\EventStore;

use Daikon\EventSourcing\Aggregate\AggregateIdInterface;
use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Daikon\EventSourcing\Aggregate\AggregateRootInterface;
use Daikon\EventSourcing\Aggregate\DomainEventSequence;
use Daikon\MessageBus\Metadata\Metadata;

final class UnitOfWork implements UnitOfWorkInterface
{
    private const MAX_RACE_ATTEMPTS = 5;

    /** @var string */
    private $aggregateRootType;

    /** @var StreamStoreInterface */
    private $streamStore;

    /** @var StreamProcessorInterface */
    private $streamProcessor;

    /** @var string */
    private $streamImplementor;

    /** @var StreamMap */
    private $trackedCommitStreams;

    /** @var int */
    private $maxRaceAttempts;

    public function __construct(
        string $aggregateRootType,
        StreamStoreInterface $streamStore,
        StreamProcessorInterface $streamProcessor = null,
        string $streamImplementor = Stream::class,
        int $maxRaceAttempts = self::MAX_RACE_ATTEMPTS
    ) {
        $this->aggregateRootType = $aggregateRootType;
        $this->streamStore = $streamStore;
        $this->streamProcessor = $streamProcessor;
        $this->streamImplementor = $streamImplementor;
        $this->trackedCommitStreams = StreamMap::makeEmpty();
        $this->maxRaceAttempts = $maxRaceAttempts;
    }

    public function commit(AggregateRootInterface $aggregateRoot, Metadata $metadata): CommitSequenceInterface
    {
        $prevStream = $this->getTrackedStream($aggregateRoot);
        $updatedStream = $prevStream->appendEvents($aggregateRoot->getTrackedEvents(), $metadata);
        $result = $this->streamStore->commit($updatedStream, $prevStream->getStreamRevision());
        $raceCount = 0;
        while ($result instanceof ConcurrencyError) {
            if (++$raceCount > $this->maxRaceAttempts) {
                throw new ConcurrencyRaceLost($prevStream->getStreamId(), $aggregateRoot->getTrackedEvents());
            }
            $prevStream = $this->streamStore->checkout($updatedStream->getStreamId());
            $conflictingEvents = $this->getConflicts($aggregateRoot, $prevStream);
            if (!$conflictingEvents->isEmpty()) {
                throw new UnresolvableConflict($prevStream->getStreamId(), $conflictingEvents);
            }
            $updatedStream = $prevStream->appendEvents($aggregateRoot->getTrackedEvents(), $metadata);
            $result = $this->streamStore->commit($updatedStream, $prevStream->getStreamRevision());
        }
        $this->trackedCommitStreams = $this->trackedCommitStreams->unregister($prevStream->getStreamId());
        return $updatedStream->getCommitRange($prevStream->getStreamRevision(), $updatedStream->getStreamRevision());
    }

    public function checkout(AggregateIdInterface $aggregateId, AggregateRevision $revision): AggregateRootInterface
    {
        /** @var $streamId StreamId */
        $streamId = StreamId::fromNative($aggregateId->toNative());
        $stream = $this->streamStore->checkout($streamId, $revision);
        $aggregateRoot = call_user_func(
            [ $this->aggregateRootType, 'reconstituteFromHistory' ],
            $aggregateId,
            $this->prepareHistory($stream, $revision)
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
            throw new \Exception("AggregateRoots must be checked out before they may be committed.");
        }
        return $stream;
    }

    private function prepareHistory(StreamInterface $stream, AggregateRevision $targetRevision): DomainEventSequence
    {
        $stream = $this->streamProcessor ? $this->streamProcessor->process($stream) : $stream;
        $history = DomainEventSequence::makeEmpty();
        foreach ($stream as $commit) {
            if (!$targetRevision->isEmpty() && $commit->getAggregateRevision()->isGreaterThan($targetRevision)) {
                break;
            }
            $history = $history->append($commit->getEventLog());
        }
        return $history;
    }

    private function getConflicts(AggregateRootInterface $aggregateRoot, StreamInterface $stream): DomainEventSequence
    {
        $conflictingEvents = DomainEventSequence::makeEmpty();
        $prevCommits = $stream->findCommitsSince($aggregateRoot->getRevision());
        foreach ($prevCommits as $previousCommit) {
            foreach ($previousCommit->getEventLog() as $previousEvent) {
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
