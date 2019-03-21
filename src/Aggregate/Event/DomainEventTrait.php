<?php
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\Aggregate\Event;

use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Daikon\EventSourcing\Aggregate\AnnotatesAggregate;
use Daikon\EventSourcing\Aggregate\Event\DomainEventInterface;

trait DomainEventTrait
{
    use AnnotatesAggregate;

    public function getAggregateRevision(): AggregateRevision
    {
        return $this->{static::getAnnotatedRevision()};
    }

    public function withAggregateRevision(AggregateRevision $aggregateRevision): DomainEventInterface
    {
        $copy = clone $this;
        $copy->{static::getAnnotatedRevision()} = $aggregateRevision;
        return $copy;
    }
}
