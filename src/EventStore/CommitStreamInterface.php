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

interface CommitStreamInterface extends \IteratorAggregate, \Countable
{
    public static function fromStreamId(
        CommitStreamId $streamId,
        string $commitImplementor = Commit::class
    ): CommitStreamInterface;

    public function appendEvents(DomainEventSequence $eventLog, Metadata $metadata): CommitStreamInterface;

    public function appendCommit(CommitInterface $commit): CommitStreamInterface;

    public function getCommitRange(CommitStreamRevision $fromRev, CommitStreamRevision $toRev = null): CommitSequence;

    public function getStreamId(): CommitStreamId;

    public function getStreamRevision(): CommitStreamRevision;

    public function getAggregateRevision(): AggregateRevision;

    public function getHead(): ?CommitInterface;
}
