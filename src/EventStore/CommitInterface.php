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

interface CommitInterface extends MessageInterface
{
    public static function make(
        CommitStreamId $streamId,
        CommitStreamRevision $streamRevision,
        DomainEventSequence $eventLog,
        Metadata $metadata
    ): CommitInterface;

    public function getStreamId(): CommitStreamId;

    public function getStreamRevision(): CommitStreamRevision;

    public function getAggregateRevision(): AggregateRevision;

    public function getEventLog(): DomainEventSequence;

    public function getMetadata(): Metadata;
}
