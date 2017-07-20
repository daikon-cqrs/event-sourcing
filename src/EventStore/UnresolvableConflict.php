<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\EventStore;

use Daikon\EventSourcing\Aggregate\DomainEventSequenceInterface;
use Daikon\EventSourcing\EventStore\Stream\StreamIdInterface;
use Exception;

final class UnresolvableConflict extends Exception
{
    /** @var StreamIdInterface */
    private $streamId;

    /** @var DomainEventSequenceInterface */
    private $conflictingEvents;

    public function __construct(StreamIdInterface $streamId, DomainEventSequenceInterface $conflictingEvents)
    {
        $this->streamId = $streamId;
        $this->conflictingEvents = $conflictingEvents;
        parent::__construct('Unable to resolve conflict for stream: '.$this->streamId);
    }

    public function getStreamId(): StreamIdInterface
    {
        return $this->streamId;
    }

    public function getConflictingEvents(): DomainEventSequenceInterface
    {
        return $this->conflictingEvents;
    }
}
