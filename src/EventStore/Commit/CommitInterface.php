<?php

/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\EventStore\Commit;

use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Daikon\EventSourcing\Aggregate\Event\DomainEventSequenceInterface;
use Daikon\EventSourcing\Aggregate\AggregateIdInterface;
use Daikon\EventSourcing\EventStore\Stream\Sequence;
use Daikon\MessageBus\MessageInterface;
use Daikon\Metadata\MetadataInterface;
use DateTimeImmutable;

interface CommitInterface extends MessageInterface
{
    public static function make(
        AggregateIdInterface $aggregateId,
        Sequence $Sequence,
        DomainEventSequenceInterface $eventLog,
        MetadataInterface $metadata
    ): CommitInterface;

    public function getAggregateId(): AggregateIdInterface;

    public function getSequence(): Sequence;

    public function getCommittedAt(): DateTimeImmutable;

    public function getAggregateRevision(): AggregateRevision;

    public function getEventLog(): DomainEventSequenceInterface;

    public function getMetadata(): MetadataInterface;
}
