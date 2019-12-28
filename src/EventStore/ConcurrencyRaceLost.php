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

final class ConcurrencyRaceLost extends Exception
{
    /** @var AggregateIdInterface */
    private $aggregateId;

    /** @var DomainEventSequenceInterface */
    private $lostEvents;

    public function __construct(AggregateIdInterface $aggregateId, DomainEventSequenceInterface $lostEvents)
    {
        $this->aggregateId = $aggregateId;
        $this->lostEvents = $lostEvents;
        parent::__construct('Unable to catchup on stream: ' . $this->aggregateId);
    }

    public function getAggregateId(): AggregateIdInterface
    {
        return $this->aggregateId;
    }

    public function getLostEvents(): DomainEventSequenceInterface
    {
        return $this->lostEvents;
    }
}
