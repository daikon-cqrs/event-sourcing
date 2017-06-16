<?php

namespace Accordia\Cqrs\EventStore;

use Accordia\MessageBus\Metadata\Metadata;
use Accordia\Cqrs\Aggregate\AggregateIdInterface;
use Accordia\Cqrs\Aggregate\AggregateRootInterface;
use Accordia\Cqrs\Aggregate\Revision;

interface UnitOfWorkInterface
{
    /**
     * @param AggregateRootInterface $aggregateRoot
     * @param Metadata $metadata
     * @return CommitSequence
     */
    public function commit(AggregateRootInterface $aggregateRoot, Metadata $metadata): CommitSequence;

    /**
     * @param AggregateIdInterface $aggregateId
     * @param Revision|null $revision
     * @return AggregateRootInterface
     */
    public function checkout(AggregateIdInterface $aggregateId, Revision $revision = null): AggregateRootInterface;
}
