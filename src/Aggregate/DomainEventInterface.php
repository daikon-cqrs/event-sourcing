<?php

namespace Daikon\Cqrs\Aggregate;

use Daikon\MessageBus\MessageInterface;

interface DomainEventInterface extends MessageInterface
{
    public function getAggregateId(): AggregateIdInterface;

    public function getAggregateRevision(): AggregateRevision;

    public function withAggregateRevision(AggregateRevision $aggregateRevision): DomainEventInterface;
}
