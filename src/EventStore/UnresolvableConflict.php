<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\EventSourcing\EventStore;

use Daikon\EventSourcing\Aggregate\Event\DomainEventSequenceInterface;
use Daikon\EventSourcing\Aggregate\AggregateIdInterface;
use Exception;

final class UnresolvableConflict extends Exception
{
    private AggregateIdInterface $aggregateId;

    private DomainEventSequenceInterface $conflictingEvents;

    public function __construct(AggregateIdInterface $aggregateId, DomainEventSequenceInterface $conflictingEvents)
    {
        $this->aggregateId = $aggregateId;
        $this->conflictingEvents = $conflictingEvents;
        parent::__construct('Unable to resolve conflict for stream: ' . $this->aggregateId);
    }

    public function getAggregateId(): AggregateIdInterface
    {
        return $this->aggregateId;
    }

    public function getConflictingEvents(): DomainEventSequenceInterface
    {
        return $this->conflictingEvents;
    }
}
