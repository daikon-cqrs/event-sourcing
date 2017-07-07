<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\EventStore;

use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Daikon\EventSourcing\Aggregate\DomainEventSequence;
use Daikon\MessageBus\MessageInterface;
use Daikon\MessageBus\Metadata\Metadata;

final class Commit implements CommitInterface
{
    /** @var StreamId */
    private $streamId;

    /** @var StreamRevision */
    private $streamRevision;

    /** @var DomainEventSequence */
    private $eventLog;

    /** @var Metadata */
    private $metadata;

    public static function make(
        StreamId $streamId,
        StreamRevision $streamRevision,
        DomainEventSequence $eventLog,
        Metadata $metadata
    ): CommitInterface {
        return new self($streamId, $streamRevision, $eventLog, $metadata);
    }

    public static function fromArray(array $state): MessageInterface
    {
        return new self(
            StreamId::fromNative($state["streamId"]),
            StreamRevision::fromNative((int)$state["streamRevision"]),
            DomainEventSequence::fromArray($state["eventLog"]),
            Metadata::fromArray($state["metadata"])
        );
    }

    public function getStreamId(): StreamId
    {
        return $this->streamId;
    }

    public function getStreamRevision(): StreamRevision
    {
        return $this->streamRevision;
    }

    public function getAggregateRevision(): AggregateRevision
    {
        return $this->eventLog->getHeadRevision();
    }

    public function getEventLog(): DomainEventSequence
    {
        return $this->eventLog;
    }

    public function getMetadata(): Metadata
    {
        return $this->metadata;
    }

    public function toArray(): array
    {
        return [
            "streamId" => $this->streamId->toNative(),
            "streamRevision" => $this->streamRevision->toNative(),
            "eventLog" => $this->eventLog->toNative(),
            "metadata" => $this->metadata->toArray()
        ];
    }

    private function __construct(
        StreamId $streamId,
        StreamRevision $streamRevision,
        DomainEventSequence $eventLog,
        Metadata $metadata
    ) {
        $this->streamId = $streamId;
        $this->streamRevision = $streamRevision;
        $this->eventLog = $eventLog;
        $this->metadata = $metadata;
    }
}
