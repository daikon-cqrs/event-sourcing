<?php

namespace Accordia\Cqrs\Aggregate;

abstract class Command implements CommandInterface
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
    private $expectedRevision;

    /**
     * @return AggregateIdInterface
     */
    public function getAggregateId(): AggregateIdInterface
    {
        return $this->aggregateId;
    }

    /**
     * @return Revision|null
     */
    public function getExpectedRevision(): ?Revision
    {
        return $this->expectedRevision;
    }

    /**
     * @return bool
     */
    public function hasExpectedRevision(): bool
    {
        return $this->expectedRevision !== null;
    }

    /**
     * @param AggregateIdInterface $aggregateId
     * @param Revision|null $expectedRevision
     */
    protected function __construct(AggregateIdInterface $aggregateId, Revision $expectedRevision = null)
    {
        $this->aggregateId = $aggregateId;
        $this->expectedRevision = $expectedRevision;
    }
}
