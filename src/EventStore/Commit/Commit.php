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
use Daikon\EventSourcing\EventStore\Stream\Sequence;
use Daikon\EventSourcing\EventStore\Stream\StreamId;
use Daikon\EventSourcing\EventStore\Stream\StreamIdInterface;
use Daikon\MessageBus\Metadata\Metadata;
use Daikon\MessageBus\Metadata\MetadataInterface;
use DateTimeImmutable;

final class Commit implements CommitInterface
{
    private const NATIVE_FORMAT = 'Y-m-d\TH:i:s.uP';

    /** @var Sequence */
    private $sequence;

    /** @var DateTimeImmutable */
    private $committedAt;

    /** @var DomainEventSequenceInterface */
    private $eventLog;

    /** @var MetadataInterface */
    private $metadata;

    public static function make(
        StreamIdInterface $streamId,
        Sequence $sequence,
        DomainEventSequenceInterface $eventLog,
        MetadataInterface $metadata
    ): CommitInterface {
        return new self($streamId, $sequence, new DateTimeImmutable, $eventLog, $metadata);
    }

    /** @param array $state */
    public static function fromNative($state): CommitInterface
    {
        Assertion::keyExists($state, 'streamId');
        Assertion::keyExists($state, 'sequence');
        Assertion::keyExists($state, 'committedAt');
        Assertion::keyExists($state, 'eventLog');
        Assertion::keyExists($state, 'metadata');
        Assertion::date($state['committedAt'], self::NATIVE_FORMAT);

        return new self(
            StreamId::fromNative($state['streamId']),
            Sequence::fromNative((int) $state['sequence']),
            DateTimeImmutable::createFromFormat(self::NATIVE_FORMAT, $state['committedAt']),
            DomainEventSequence::fromNative($state['eventLog']),
            Metadata::fromNative($state['metadata'])
        );
    }

    public function getStreamId(): StreamIdInterface
    {
        return $this->streamId;
    }

    public function getSequence(): Sequence
    {
        return $this->sequence;
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
            'sequence' => $this->sequence->toNative(),
            'committedAt' => $this->committedAt->format(self::NATIVE_FORMAT),
            'eventLog' => $this->eventLog->toNative(),
            'metadata' => $this->metadata->toNative()
        ];
    }

    private function __construct(
        StreamIdInterface $streamId,
        Sequence $sequence,
        DateTimeImmutable $committedAt,
        DomainEventSequenceInterface $eventLog,
        MetadataInterface $metadata
    ) {
        $this->streamId = $streamId;
        $this->sequence = $sequence;
        $this->committedAt = $committedAt;
        $this->eventLog = $eventLog;
        $this->metadata = $metadata;
    }
}
