<?php

namespace Accordia\Cqrs\Aggregate;

abstract class DomainEvent implements DomainEventInterface
{
    /**
     * @var \Accordia\Cqrs\Aggregate\AggregateId
     * @buzz::fromArray->fromNative
     */
    private $aggregateId;

    /**
     * @var \Accordia\Cqrs\Aggregate\AggregateRevision
     * @buzz::fromArray->fromNative
     */
    private $aggregateRevision;

    /**
     * @return AggregateIdInterface
     */
    public function getAggregateId(): AggregateIdInterface
    {
        return $this->aggregateId;
    }

    /**
     * @return AggregateRevision
     */
    public function getAggregateRevision(): AggregateRevision
    {
        return $this->aggregateRevision;
    }

    /**
     * @param AggregateRevision $aggregateRevision
     * @return DomainEventInterface
     */
    public function withAggregateRevision(AggregateRevision $aggregateRevision): DomainEventInterface
    {
        $copy = clone $this;
        $copy->aggregateRevision = $aggregateRevision;
        return $copy;
    }

    /**
     * @param AggregateIdInterface $aggregateId
     * @param AggregateRevision|null $aggregateRevision
     */
    protected function __construct(AggregateIdInterface $aggregateId, AggregateRevision $aggregateRevision = null)
    {
        $this->aggregateId = $aggregateId;
        $this->aggregateRevision = $aggregateRevision ?? AggregateRevision::makeEmpty();
    }
}
