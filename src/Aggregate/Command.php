<?php

namespace Daikon\Cqrs\Aggregate;

abstract class Command implements CommandInterface
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
    private $knownAggregateRevision;

    /**
     * @return AggregateIdInterface
     */
    public function getAggregateId(): AggregateIdInterface
    {
        return $this->aggregateId;
    }

    /**
     * @return AggregateRevision|null
     */
    public function getKnownAggregateRevision(): ?AggregateRevision
    {
        return $this->knownAggregateRevision;
    }

    /**
     * @return bool
     */
    public function hasKnownAggregateRevision(): bool
    {
        return $this->knownAggregateRevision !== null;
    }

    /**
     * @param AggregateIdInterface $aggregateId
     * @param AggregateRevision|null $knownAggregateRevision
     */
    protected function __construct(AggregateIdInterface $aggregateId, AggregateRevision $knownAggregateRevision = null)
    {
        $this->aggregateId = $aggregateId;
        $this->knownAggregateRevision = $knownAggregateRevision;
    }
}
