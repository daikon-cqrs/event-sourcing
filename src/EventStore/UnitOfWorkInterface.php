<?php
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\EventStore;

use Daikon\EventSourcing\Aggregate\AggregateIdInterface;
use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Daikon\EventSourcing\Aggregate\AggregateRootInterface;
use Daikon\EventSourcing\EventStore\Commit\CommitSequenceInterface;
use Daikon\MessageBus\Metadata\Metadata;

interface UnitOfWorkInterface
{
    public function commit(AggregateRootInterface $aggregateRoot, Metadata $metadata): CommitSequenceInterface;

    public function checkout(AggregateIdInterface $aggregateId, AggregateRevision $revision): AggregateRootInterface;
}
