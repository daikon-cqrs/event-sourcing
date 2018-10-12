<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\EventStore\Commit;

use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Daikon\EventSourcing\Aggregate\Event\DomainEventSequence;
use Daikon\EventSourcing\Aggregate\Event\DomainEventSequenceInterface;
use Daikon\EventSourcing\EventStore\Stream\StreamId;
use Daikon\EventSourcing\EventStore\Stream\StreamIdInterface;
use Daikon\EventSourcing\EventStore\Stream\StreamRevision;
use Daikon\MessageBus\MessageInterface;
use Daikon\MessageBus\Metadata\Metadata;
use Daikon\MessageBus\Metadata\MetadataInterface;

final class Commit implements CommitInterface
{
    /** @var StreamIdInterface */
    private $streamId;

    /** @var StreamRevision */
    private $streamRevision;

    /** @var DomainEventSequenceInterface */
    private $eventLog;

    /** @var MetadataInterface */
    private $metadata;

    public static function make(
        StreamIdInterface $streamId,
        StreamRevision $streamRevision,
        DomainEventSequenceInterface $eventLog,
        MetadataInterface $metadata
    ): CommitInterface {
        return new self($streamId, $streamRevision, $eventLog, $metadata);
    }

    public static function fromArray(array $state): MessageInterface
    {
        return new self(
            StreamId::fromNative($state['streamId']),
            StreamRevision::fromNative((int)$state['streamRevision']),
            DomainEventSequence::fromArray($state['eventLog']),
            Metadata::fromArray($state['metadata'])
        );
    }

    public function getStreamId(): StreamIdInterface
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

    public function getEventLog(): DomainEventSequenceInterface
    {
        return $this->eventLog;
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
    }

    public function toArray(): array
    {
        return [
            '@type' => static::class,
            'streamId' => $this->streamId->toNative(),
            'streamRevision' => $this->streamRevision->toNative(),
            'eventLog' => $this->eventLog->toNative(),
            'metadata' => $this->metadata->toArray()
        ];
    }

    private function __construct(
        StreamIdInterface $streamId,
        StreamRevision $streamRevision,
        DomainEventSequenceInterface $eventLog,
        MetadataInterface $metadata
    ) {
        $this->streamId = $streamId;
        $this->streamRevision = $streamRevision;
        $this->eventLog = $eventLog;
        $this->metadata = $metadata;
    }
}
