<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Cqrs\Aggregate;

use Daikon\MessageBus\FromArrayTrait;
use Daikon\MessageBus\ToArrayTrait;

abstract class DomainEvent implements DomainEventInterface
{
    use FromArrayTrait;
    use ToArrayTrait;

    /**
     * @MessageBus::deserialize(\Daikon\Cqrs\Aggregate\AggregateId::fromNative)
     */
    private $aggregateId;

    /**
     * @MessageBus::deserialize(\Daikon\Cqrs\Aggregate\AggregateRevision::fromNative)
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
