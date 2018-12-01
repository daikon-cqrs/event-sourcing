<?php
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\EventStore;

use Daikon\EventSourcing\Aggregate\Event\DomainEventSequenceInterface;
use Daikon\EventSourcing\EventStore\Stream\StreamIdInterface;

final class ConcurrencyRaceLost extends \Exception
{
    /** @var StreamIdInterface */
    private $streamId;

    /** @var  DomainEventSequenceInterface */
    private $lostEvents;

    public function __construct(StreamIdInterface $streamId, DomainEventSequenceInterface $lostEvents)
    {
        $this->streamId = $streamId;
        $this->lostEvents = $lostEvents;
        parent::__construct('Unable to catchup on stream: '.$this->streamId);
    }

    public function getStreamId(): StreamIdInterface
    {
        return $this->streamId;
    }

    public function getLostEvents(): DomainEventSequenceInterface
    {
        return $this->lostEvents;
    }
}
