<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\Aggregate;

abstract class DomainEvent implements DomainEventInterface
{
    /** @var AggregateIdInterface */
    private $aggregateId;

    /** @var AggregateRevision */
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

    public function toArray(): array
    {
        return [
            'aggregateId' => $this->aggregateId->toNative(),
            'aggregateRevision' => $this->aggregateRevision->toNative()
        ];
    }

    protected function __construct(AggregateIdInterface $aggregateId, AggregateRevision $aggregateRevision = null)
    {
        $this->aggregateId = $aggregateId;
        $this->aggregateRevision = $aggregateRevision ?? AggregateRevision::makeEmpty();
    }
}
