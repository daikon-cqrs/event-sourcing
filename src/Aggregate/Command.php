<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\Aggregate;

abstract class Command implements CommandInterface
{
    /** @var AggregateIdInterface */
    private $aggregateId;

    /** @var AggregateRevision */
    private $knownAggregateRevision;

    public function getAggregateId(): AggregateIdInterface
    {
        return $this->aggregateId;
    }

    public function getKnownAggregateRevision(): AggregateRevision
    {
        return $this->knownAggregateRevision;
    }

    public function hasKnownAggregateRevision(): bool
    {
        return !$this->knownAggregateRevision->isEmpty();
    }

    public function toArray(): array
    {
        return [
            'aggregateId' => $this->aggregateId->toNative(),
            'knownAggregateRevision' => $this->knownAggregateRevision->toNative()
        ];
    }

    protected function __construct(AggregateIdInterface $aggregateId, AggregateRevision $knownAggregateRevision = null)
    {
        $this->aggregateId = $aggregateId;
        $this->knownAggregateRevision = $knownAggregateRevision ?? AggregateRevision::makeEmpty();
    }
}
