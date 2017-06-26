<?php

namespace Daikon\Cqrs\EventStore;

use Daikon\MessageBus\Metadata\Metadata;
use Daikon\Cqrs\Aggregate\AggregateIdInterface;
use Daikon\Cqrs\Aggregate\AggregateRootInterface;

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
     * @param CommitStreamRevision|null $revision
     * @return AggregateRootInterface
     */
    public function checkout(
        AggregateIdInterface $aggregateId,
        CommitStreamRevision $revision = null
    ): AggregateRootInterface;
}
