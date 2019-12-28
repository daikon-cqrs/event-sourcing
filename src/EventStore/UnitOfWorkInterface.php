<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\EventSourcing\EventStore;

use Daikon\EventSourcing\Aggregate\AggregateIdInterface;
use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Daikon\EventSourcing\Aggregate\AggregateRootInterface;
use Daikon\EventSourcing\EventStore\Commit\CommitSequenceInterface;
use Daikon\Metadata\MetadataInterface;

interface UnitOfWorkInterface
{
    public function commit(AggregateRootInterface $aggregateRoot, MetadataInterface $metadata): CommitSequenceInterface;

    public function checkout(AggregateIdInterface $aggregateId, AggregateRevision $revision): AggregateRootInterface;
}
