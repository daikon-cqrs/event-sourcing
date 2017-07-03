<?php

namespace Daikon\Cqrs\EventStore;

use Daikon\MessageBus\Metadata\Metadata;
use Daikon\Cqrs\Aggregate\AggregateIdInterface;
use Daikon\Cqrs\Aggregate\AggregateRootInterface;
use Daikon\Cqrs\Aggregate\DomainEventSequence;

final class UnitOfWork implements UnitOfWorkInterface
{
    private $aggregateRootType;

    private $streamStore;

    private $streamProcessor;

    private $streamImplementor;

    private $trackedCommitStreams;

    public function __construct(
        string $aggregateRootType,
        StreamStoreInterface $streamStore,
        StreamProcessorInterface $streamProcessor,
        string $streamImplementor = CommitStream::class
    ) {
        $this->aggregateRootType = $aggregateRootType;
        $this->streamStore = $streamStore;
        $this->streamProcessor = $streamProcessor;
        $this->streamImplementor = $streamImplementor;
        $this->trackedCommitStreams = CommitStreamMap::makeEmpty();
    }

    public function commit(AggregateRootInterface $aggregateRoot, Metadata $metadata): CommitSequence
    {
        $streamId = CommitStreamId::fromNative($aggregateRoot->getIdentifier());
        $tailRevision = $aggregateRoot->getTrackedEvents()->getTailRevision();
        if ($this->trackedCommitStreams->has((string)$streamId)) {
            $stream = $this->trackedCommitStreams->get((string)$streamId);
            $this->trackedCommitStreams = $this->trackedCommitStreams->unregister($stream);
        } elseif ($tailRevision->isInitial()) {
            $stream = $this->streamImplementor::fromStreamId($streamId);
        } else {
            throw new \Exception("Existing aggregate-roots must be checked out before they may be comitted.");
        }
        $stream = $stream->appendEvents($aggregateRoot->getTrackedEvents(), $metadata);
        $knownHead = $stream->getStreamRevision();
        if (!$this->streamStore->commit($stream, $knownHead)) {
            $this->trackedCommitStreams = $this->trackedCommitStreams->register($stream);
            throw new \Exception("Failed to store commit-stream with stream-id: ".$stream->getStreamId());
        }
        return $stream->getCommitRange($knownHead, $stream->getStreamRevision());
    }

    public function checkout(
        AggregateIdInterface $aggregateId,
        CommitStreamRevision $revision = null
    ): AggregateRootInterface {
        $streamId = CommitStreamId::fromNative($aggregateId->toNative());
        $stream = $this->streamStore->checkout($streamId, $revision);
        $history = DomainEventSequence::makeEmpty();
        foreach ($this->streamProcessor->process($stream) as $commit) {
            $history = $history->append($commit->getEventLog());
        }
        $aggregateRoot = $this->aggregateRootType::reconstituteFromHistory($history);
        $this->trackedCommitStreams = $this->trackedCommitStreams->register($stream);
        return $aggregateRoot;
    }
}
