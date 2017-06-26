<?php

namespace Daikon\Cqrs\EventStore;

use Daikon\MessageBus\Metadata\Metadata;
use Daikon\Cqrs\Aggregate\AggregateIdInterface;
use Daikon\Cqrs\Aggregate\AggregateRootInterface;
use Daikon\Cqrs\Aggregate\DomainEventSequence;

final class UnitOfWork implements UnitOfWorkInterface
{
    /**
     * @var string
     */
    private $aggregateRootType;

    /**
     * @var PersistenceAdapterInterface
     */
    private $persistenceAdapter;

    /**
     * @var StreamProcessorInterface
     */
    private $streamProcessor;

    /**
     * @var string
     */
    private $streamImplementor;

    /**
     * @var CommitStreamMap|null
     */
    private $trackedCommitStreams;

    /**
     * @param string $aggregateRootType
     * @param PersistenceAdapterInterface $persistenceAdapter
     * @param StreamProcessorInterface $streamProcessor
     * @param string $streamImplementor
     */
    public function __construct(
        string $aggregateRootType,
        PersistenceAdapterInterface $persistenceAdapter,
        StreamProcessorInterface $streamProcessor,
        string $streamImplementor = CommitStream::class
    ) {
        $this->aggregateRootType = $aggregateRootType;
        $this->persistenceAdapter = $persistenceAdapter;
        $this->streamProcessor = $streamProcessor;
        $this->streamImplementor = $streamImplementor;
        $this->trackedCommitStreams = CommitStreamMap::makeEmpty();
    }

    /**
     * @param AggregateRootInterface $aggregateRoot
     * @param Metadata $metadata
     * @return CommitSequence
     * @throws \Exception
     */
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
        if (!$this->persistenceAdapter->storeStream($stream, $knownHead)) {
            $this->trackedCommitStreams = $this->trackedCommitStreams->register($stream);
            throw new \Exception("Failed to store commit-stream with stream-id: ".$stream->getStreamId());
        }
        return $stream->getCommitRange($knownHead, $stream->getStreamRevision());
    }

    /**
     * @param AggregateIdInterface $aggregateId
     * @param CommitStreamRevision|null $revision
     * @return AggregateRootInterface
     */
    public function checkout(
        AggregateIdInterface $aggregateId,
        CommitStreamRevision $revision = null
    ): AggregateRootInterface {
        $streamId = CommitStreamId::fromNative($aggregateId->toNative());
        $stream = $this->persistenceAdapter->loadStream($streamId, $revision);
        $history = DomainEventSequence::makeEmpty();
        foreach ($this->streamProcessor->process($stream) as $commit) {
            $history = $history->append($commit->getEventLog());
        }
        $aggregateRoot = $this->aggregateRootType::reconstituteFromHistory($history);
        $this->trackedCommitStreams = $this->trackedCommitStreams->register($stream);
        return $aggregateRoot;
    }
}
