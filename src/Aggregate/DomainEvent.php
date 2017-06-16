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
     * @var \Accordia\Cqrs\Aggregate\Revision
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
     * @return Revision
     */
    public function getAggregateRevision(): Revision
    {
        return $this->aggregateRevision;
    }

    /**
     * @param Revision $aggregateRevision
     * @return DomainEventInterface
     */
    public function withAggregateRevision(Revision $aggregateRevision): DomainEventInterface
    {
        $copy = clone $this;
        $copy->aggregateRevision = $aggregateRevision;
        return $copy;
    }

    /**
     * @param AggregateIdInterface $aggregateId
     * @param Revision|null $aggregateRevision
     */
    protected function __construct(AggregateIdInterface $aggregateId, Revision $aggregateRevision = null)
    {
        $this->aggregateId = $aggregateId;
        $this->aggregateRevision = $aggregateRevision ?? Revision::makeEmpty();
    }
}
