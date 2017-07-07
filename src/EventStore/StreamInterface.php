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
use Daikon\MessageBus\Metadata\Metadata;

interface StreamInterface extends \IteratorAggregate, \Countable
{
    public static function fromStreamId(StreamId $streamId, string $commitImplementor = Commit::class): StreamInterface;

    public function appendEvents(DomainEventSequence $eventLog, Metadata $metadata): StreamInterface;

    public function appendCommit(CommitInterface $commit): StreamInterface;

    public function getCommitRange(StreamRevision $fromRev, StreamRevision $toRev = null): CommitSequence;

    public function getStreamId(): StreamId;

    public function getStreamRevision(): StreamRevision;

    public function getAggregateRevision(): AggregateRevision;

    public function getHead(): ?CommitInterface;

    public function findCommitsSince(AggregateRevision $incomingRevision): CommitSequence;
}
