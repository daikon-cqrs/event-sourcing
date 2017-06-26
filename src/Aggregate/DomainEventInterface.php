<?php

namespace Daikon\Cqrs\Aggregate;

use Daikon\MessageBus\MessageInterface;

interface DomainEventInterface extends MessageInterface
{
    /**
     * @return AggregateIdInterface
     */
    public function getAggregateId(): AggregateIdInterface;

    /**
     * @return AggregateRevision
     */
    public function getAggregateRevision(): AggregateRevision;

    /**
     * @param AggregateRevision $aggregateRevision
     * @return DomainEventInterface
     */
    public function withAggregateRevision(AggregateRevision $aggregateRevision): DomainEventInterface;
}
