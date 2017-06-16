<?php

namespace Accordia\Cqrs\EventStore;

use Accordia\MessageBus\Metadata\Metadata;
use Accordia\Cqrs\Aggregate\AggregateIdInterface;
use Accordia\Cqrs\Aggregate\AggregateRootInterface;
use Accordia\Cqrs\Aggregate\DomainEventList;
use Accordia\Cqrs\Aggregate\Revision;

class UnitOfWork implements UnitOfWorkInterface
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
     * @param string $streamImplementor
     * @param CommitStreamMap|null $trackedCommitStreams
     */
    public function __construct(
        string $aggregateRootType,
        PersistenceAdapterInterface $persistenceAdapter,
        string $streamImplementor = CommitStream::class,
        CommitStreamMap $trackedCommitStreams = null
    ) {
        $this->aggregateRootType = $aggregateRootType;
        $this->persistenceAdapter = $persistenceAdapter;
        $this->streamImplementor = $streamImplementor;
        $this->trackedCommitStreams = $trackedCommitStreams ?? new CommitStreamMap;
    }

    /**
     * @param AggregateRootInterface $aggregateRoot
     * @param Metadata $metadata
     * @return CommitSequence
     * @throws \Exception
     */
    public function commit(AggregateRootInterface $aggregateRoot, Metadata $metadata): CommitSequence
    {
        $streamId = StreamId::fromNative($aggregateRoot->getIdentifier());
        $tailRevision = $aggregateRoot->getTrackedEvents()->getTailRevision();
        if ($this->trackedCommitStreams->has((string)$streamId)) {
            $stream = $this->trackedCommitStreams->get((string)$streamId);
            $this->trackedCommitStreams = $this->trackedCommitStreams->remove($stream);
        } else if ($tailRevision->isInitial()) {
            $stream = $this->streamImplementor::fromStreamId($streamId);
        } else {
            throw new \Exception("Existing aggregate-roots must be checked out before they may be comitted.");
        }
        $knownHead = $stream->getStreamRevision();
        $stream = $stream->appendEvents($aggregateRoot->getTrackedEvents(), $metadata);
        if (!$this->persistenceAdapter->storeStream($stream, $knownHead)) {
            $this->trackedCommitStreams = $this->trackedCommitStreams->register($stream);
            throw new \Exception("Failed to store commit-stream with stream-id: ".$stream->getStreamId());
        }
        return $stream->getCommitRange($knownHead, $stream->getStreamRevision());
    }

    /**
     * @param AggregateIdInterface $aggregateId
     * @param Revision|null $revision
     * @return AggregateRootInterface
     */
    public function checkout(AggregateIdInterface $aggregateId, Revision $revision = null): AggregateRootInterface
    {
        $streamId = StreamId::fromNative($aggregateId->toNative());
        $stream = $this->persistenceAdapter->loadStream($streamId, $revision);
        $history = new DomainEventList;
        foreach ($stream as $commit) {
            $history = $history->append($commit->getEventLog());
            if ($revision && $revision->equals($commit->getStreamRevision())) {
                break;
            }
        }
        $aggregateRoot = $this->aggregateRootType::reconstituteFromHistory($history);
        $this->trackedCommitStreams = $this->trackedCommitStreams->register($stream);
        return $aggregateRoot;
    }
}
