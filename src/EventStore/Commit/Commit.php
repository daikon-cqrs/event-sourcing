<?php

/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\EventStore\Commit;

use Assert\Assertion;
use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Daikon\EventSourcing\Aggregate\Event\DomainEventSequence;
use Daikon\EventSourcing\Aggregate\Event\DomainEventSequenceInterface;
use Daikon\EventSourcing\EventStore\Stream\StreamId;
use Daikon\EventSourcing\EventStore\Stream\StreamIdInterface;
use Daikon\EventSourcing\EventStore\Stream\StreamRevision;
use Daikon\MessageBus\Metadata\Metadata;
use Daikon\MessageBus\Metadata\MetadataInterface;
use DateTimeImmutable;

final class Commit implements CommitInterface
{
    private const NATIVE_FORMAT = "Y-m-d\TH:i:s.uP";

    /** @var StreamIdInterface */
    private $streamId;

    /** @var StreamRevision */
    private $streamRevision;

    /** @var DateTimeImmutable */
    private $committedAt;

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
        return new self($streamId, $streamRevision, new DateTimeImmutable, $eventLog, $metadata);
    }

    /** @param array $state */
    public static function fromNative($state): CommitInterface
    {
        Assertion::keyExists($state, "streamId");
        Assertion::keyExists($state, "eventLog");
        Assertion::keyExists($state, "committedAt");
        Assertion::keyExists($state, "metadata");
        Assertion::date($state['committedAt'], self::NATIVE_FORMAT);

        return new self(
            StreamId::fromNative($state['streamId']),
            StreamRevision::fromNative((int) $state['committedAt']),
            DateTimeImmutable::createFromFormat($state['committedAt'], self::NATIVE_FORMAT),
            DomainEventSequence::fromNative($state['eventLog']),
            Metadata::fromNative($state['metadata'])
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

    public function getCommittedAt(): DateTimeImmutable
    {
        return $this->committedAt;
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

    public function toNative(): array
    {
        return [
            '@type' => self::class,
            'streamId' => $this->streamId->toNative(),
            'streamRevision' => $this->streamRevision->toNative(),
            'committedAt' => $this->committedAt->format(self::NATIVE_FORMAT),
            'eventLog' => $this->eventLog->toNative(),
            'metadata' => $this->metadata->toNative()
        ];
    }

    private function __construct(
        StreamIdInterface $streamId,
        StreamRevision $streamRevision,
        DateTimeImmutable $committedAt,
        DomainEventSequenceInterface $eventLog,
        MetadataInterface $metadata
    ) {
        $this->streamId = $streamId;
        $this->streamRevision = $streamRevision;
        $this->committedAt = $committedAt;
        $this->eventLog = $eventLog;
        $this->metadata = $metadata;
    }
}
