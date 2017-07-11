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
use Daikon\EventSourcing\Aggregate\DomainEventSequenceInterface;
use Daikon\MessageBus\MessageInterface;
use Daikon\MessageBus\Metadata\Metadata;

interface CommitInterface extends MessageInterface
{
    public static function make(
        StreamId $streamId,
        StreamRevision $streamRevision,
        DomainEventSequenceInterface $eventLog,
        Metadata $metadata
    ): CommitInterface;

    public function getStreamId(): StreamId;

    public function getStreamRevision(): StreamRevision;

    public function getAggregateRevision(): AggregateRevision;

    public function getEventLog(): DomainEventSequenceInterface;

    public function getMetadata(): Metadata;
}
