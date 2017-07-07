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
        $stream = $this->getStream($aggregateRoot)->appendEvents($aggregateRoot->getTrackedEvents(), $metadata);
        $knownHead = $stream->getStreamRevision();
        $result = $this->streamStore->commit($stream, $knownHead);
        if ($result instanceof StoreError) {
            $this->trackedCommitStreams = $this->trackedCommitStreams->register($stream);
            throw new \Exception("Failed to store commit-stream with stream-id: ".$stream->getStreamId());
        }
        return $stream->getCommitRange($knownHead, $stream->getStreamRevision());
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

    private function getStream(AggregateRootInterface $aggregateRoot): StreamInterface
    {
        $streamId = StreamId::fromNative((string)$aggregateRoot->getIdentifier());
        $tailRevision = $aggregateRoot->getTrackedEvents()->getTailRevision();
        if ($this->trackedCommitStreams->has((string)$streamId)) {
            $stream = $this->trackedCommitStreams->get((string)$streamId);
            $this->trackedCommitStreams = $this->trackedCommitStreams->unregister($stream);
        } elseif ($tailRevision->isInitial()) {
            $stream = call_user_func([ $this->streamImplementor, 'fromStreamId' ], $streamId);
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
}
