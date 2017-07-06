<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Cqrs\EventStore;

use Daikon\Cqrs\Aggregate\AggregateIdInterface;
use Daikon\Cqrs\Aggregate\AggregateRootInterface;
use Daikon\Cqrs\Aggregate\DomainEventSequence;
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

    /** @var CommitStreamMap */
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
        $streamId = CommitStreamId::fromNative((string)$aggregateRoot->getIdentifier());
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
            foreach ($commit->getEventLog() as $event) {
                $history = $history->push($event);
            }
        }
        $aggregateRoot = $this->aggregateRootType::reconstituteFromHistory($aggregateId, $history);
        $this->trackedCommitStreams = $this->trackedCommitStreams->register($stream);
        return $aggregateRoot;
    }
}
