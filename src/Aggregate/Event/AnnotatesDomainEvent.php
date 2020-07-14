<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\EventSourcing\Aggregate\Event;

use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Daikon\EventSourcing\Aggregate\AnnotatesForAggregate;

trait AnnotatesDomainEvent
{
    use AnnotatesForAggregate;

    public function getAggregateRevision(): AggregateRevision
    {
        return $this->{static::getAnnotatedRevision()};
    }

    /** @return static */
    public function withAggregateRevision(AggregateRevision $aggregateRevision): self
    {
        $copy = clone $this;
        $copy->{static::getAnnotatedRevision()} = $aggregateRevision;
        return $copy;
    }
}
