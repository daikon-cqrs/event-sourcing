<?php

namespace Accordia\Cqrs\Aggregate;

use Accordia\MessageBus\MessageInterface;

interface DomainEventInterface extends MessageInterface
{
    /**
     * @return AggregateIdInterface
     */
    public function getAggregateId(): AggregateIdInterface;

    /**
     * @return Revision
     */
    public function getAggregateRevision(): Revision;

    /**
     * @param Revision $aggregateRevision
     * @return DomainEventInterface
     */
    public function withAggregateRevision(Revision $aggregateRevision): DomainEventInterface;
}
