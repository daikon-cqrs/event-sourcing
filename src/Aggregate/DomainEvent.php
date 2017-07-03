<?php

namespace Daikon\Cqrs\Aggregate;

abstract class DomainEvent implements DomainEventInterface
{
    /**
     * @var \Daikon\Cqrs\Aggregate\AggregateId
     * @buzz::fromArray->fromNative
     */
    private $aggregateId;

    /**
     * @var \Daikon\Cqrs\Aggregate\AggregateRevision
     * @buzz::fromArray->fromNative
     */
    private $aggregateRevision;

    public function getAggregateId(): AggregateIdInterface
    {
        return $this->aggregateId;
    }

    public function getAggregateRevision(): AggregateRevision
    {
        return $this->aggregateRevision;
    }

    public function withAggregateRevision(AggregateRevision $aggregateRevision): DomainEventInterface
    {
        $copy = clone $this;
        $copy->aggregateRevision = $aggregateRevision;
        return $copy;
    }

    protected function __construct(AggregateIdInterface $aggregateId, AggregateRevision $aggregateRevision = null)
    {
        $this->aggregateId = $aggregateId;
        $this->aggregateRevision = $aggregateRevision ?? AggregateRevision::makeEmpty();
    }
}
