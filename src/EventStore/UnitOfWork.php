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
    private const MAX_RESOLUTION_ATTEMPTS = 5;

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

    public function __construct(
        string $aggregateRootType,
        StreamStoreInterface $streamStore,
        StreamProcessorInterface $streamProcessor = null,
        string $streamImplementor = Stream::class
    ) {
        $this->aggregateRootType = $aggregateRootType;
        $this->streamStore = $streamStore;
        $this->streamProcessor = $streamProcessor;
        $this->streamImplementor = $streamImplementor;
        $this->trackedCommitStreams = StreamMap::makeEmpty();
    }

    public function commit(AggregateRootInterface $aggregateRoot, Metadata $metadata): CommitSequence
    {
        $prevStream = $this->getTrackedStream($aggregateRoot);
        $updatedStream = $prevStream->appendEvents($aggregateRoot->getTrackedEvents(), $metadata);
        $result = $this->streamStore->commit($updatedStream, $prevStream->getStreamRevision());
        $resolutionAttempts = 0;
        while ($result instanceof PossibleConflict) {
            $conflictingStream = $this->streamStore->checkout($updatedStream->getStreamId());
            $conflictingEvents = $this->detectConflictingEvents($aggregateRoot, $conflictingStream);
            if (!$conflictingEvents->isEmpty()) {
                throw new UnresolvableConflict($conflictingStream->getStreamId(), $conflictingEvents);
            }
            $resolvedStream = $conflictingStream->appendEvents($aggregateRoot->getTrackedEvents());
            $result = $this->streamStore->commit($resolvedStream, $conflictingStream->getStreamRevision());
            if (++$resolutionAttempts >= self::MAX_RESOLUTION_ATTEMPTS) {
                throw new ConcurrencyRaceLost($conflictingStream->getStreamId(), $aggregateRoot->getTrackedEvents());
            }
        }
        $this->trackedCommitStreams = $this->trackedCommitStreams->unregister($prevStream);
        return $updatedStream->getCommitRange($prevStream->getStreamRevision(), $updatedStream->getStreamRevision());
    }

    public function checkout(AggregateIdInterface $aggregateId, AggregateRevision $revision): AggregateRootInterface
    {
        $streamId = StreamId::fromNative($aggregateId->toNative());
        $stream = $this->streamStore->checkout($streamId, $revision);
        $aggregateRoot = call_user_func(
            [ $this->aggregateRootType, 'reconstituteFromHistory' ],
            $aggregateId,
            $this->buildEventHistory($stream)
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
            throw new \Exception("Existing aggregate-roots must be checked out before they may be comitted.");
        }
        return $stream;
    }

    private function buildEventHistory(StreamInterface $stream, AggregateRevision $to): DomainEventSequence
    {
        $history = DomainEventSequence::makeEmpty();
        if ($this->streamProcessor) {
            $stream = $this->streamProcessor->process($stream);
        }
        foreach ($stream as $commit) {
            if (!$to->isEmpty() && $commit->getAggregateRevision()->isGreaterThan($to)) {
                break;
            }
            foreach ($commit->getEventLog() as $event) {
                $history = $history->push($event);
            }
        }
        return $history;
    }

    private function detectConflictingEvents(AggregateRootInterface $aggregateRoot, StreamInterface $stream): array
    {
        $conflictingEvents = [];
        foreach ($newEvents as $newEvent) {
            foreach ($stream->findCommitsSince($aggregateRoot->getRevision()) as $previousCommit) {
                foreach ($previousCommit->getEventLog() as $previousEvent) {
                    if ($newEvent->conflictsWith($previousEvent)) {
                        $conflictingEvents[] = [ $previousEvent, $newEvent ];
                    }
                }
            }
        }
        return $conflictingEvents;
    }
}
