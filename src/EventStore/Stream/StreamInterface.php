<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\EventSourcing\EventStore\Stream;

use Countable;
use Daikon\EventSourcing\Aggregate\AggregateIdInterface;
use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Daikon\EventSourcing\Aggregate\Event\DomainEventSequenceInterface;
use Daikon\EventSourcing\EventStore\Commit\Commit;
use Daikon\EventSourcing\EventStore\Commit\CommitInterface;
use Daikon\EventSourcing\EventStore\Commit\CommitSequenceInterface;
use Daikon\Metadata\MetadataInterface;
use IteratorAggregate;

interface StreamInterface extends IteratorAggregate, Countable
{
    public static function fromAggregateId(
        AggregateIdInterface $aggregateId,
        string $commitImplementor = Commit::class
    ): self;

    public function appendEvents(DomainEventSequenceInterface $eventLog, MetadataInterface $metadata): self;

    public function appendCommit(CommitInterface $commit): self;

    public function getCommitRange(Sequence $fromRev, Sequence $toRev = null): CommitSequenceInterface;

    public function getAggregateId(): AggregateIdInterface;

    public function getHeadSequence(): Sequence;

    public function getHeadRevision(): AggregateRevision;

    public function getHead(): CommitInterface;

    public function findCommitsSince(AggregateRevision $incomingRevision): CommitSequenceInterface;

    public function isEmpty(): bool;
}
