<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\EventStore;

use Exception;

final class UnresolvableConflict extends Exception
{
    /** @var StreamId */
    private $streamId;

    /** @var array */
    private $conflictingEvents;

    public function __construct(StreamId $streamId, array $conflictingEvents)
    {
        $this->streamId = $streamId;
        $this->conflictingEvents = $conflictingEvents;
        parent::__construct('Unable to resolve conflict for stream: '.$this->streamId);
    }

    public function getStreamId(): StreamId
    {
        return $this->streamId;
    }

    public function getConflictingEvents(): array
    {
        return $this->conflictingEvents;
    }
}
